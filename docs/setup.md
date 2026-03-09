# Windows + WSL2 + Docker Desktop + Laravel Sail Setup

## WSL2 の有効化

PowerShell（管理者）で

```html
wsl --install
```

#### BIOS で仮想化が有効になっているかの確認

Virtualization Technology (VT-X)を有効にするには(lenovo)：https://support.lenovo.com/jp/ja/solutions/ht500006

> 多くのWindows 10 PC、およびWindows 11が初期搭載されているすべてのPCでは、既存で仮想マシンプラットフォームが有効になっています


## Docker Desktop の初期設定

公式サイトからDocker Desktop for Windowsを入れる。

#### インストール時の注意事項

- 「Use WSL 2 instead of Hyper-V」の項目にチェックをいれる。
- 「Install required Windows components for WSL 2」などが出たらチェック。
（すでにWSLが入っていても、ここで必要なコンポーネントを入れてくれる）
- 「Use Windows containers instead of Linux containers」系の選択がどこかで出たら
→ Linux containers を使う（Windows containersはOFF/選ばない）

### WSL 連携

DockerDesktop → Settings → Resources → WSL Integration →

- Enable integration with my default WSL distro → ON
- Ubuntu→ ON

### リソース割り当て(念のため)

`%UserProfile%\.wslconfig`を作成する。

```html
[wsl2]
memory=6GB
processors=4
swap=2GB
```

#### 目安

- CPU: 2〜4
- Memory: 4GB〜8GB
- Swap: 1〜2GB
- Disk image size: 20〜40GB（気にしなくていい）

#### 適用方法

①`.wslconfig` を保存

②PowerShellで以下を実行

```
wsl --shutdown
```

③Docker Desktopを再起動

④Ubuntuを開き直す

## Ubuntu の起動

初回でユーザー名・パスワードを作成する。

### 起こりがちなエラー ”getpwuid(1000) failed”

UID1000は最初のログインユーザーに割り当てられる標準的なユーザーID。

もし以下を入力しても何も返らないならユーザー情報が取れていない。

```html
getent passwd 1000
```

解決策として、`/etc/wsl.conf` にデフォルトユーザーを明示するのが一般的。

(DockerDesktopを閉じてからやるほうが安全)

```
sudosh-c'printf "[user]\ndefault=hogehoge\n" > /etc/wsl.conf'
exit
```

その後PowerShellで`wsl --shutdown`してUbuntuを再起動する。

#### 原因

> DockerDesktopは裏でWSLディストリを常駐・再起動する。そのため、WSL初回セットアップの最中にDockerがWSLを別用途で触って、タイミング競合を起こすことがある。
回避策は「新しいUbuntuの初回セットアップが終わるまで、Docker Desktop の再起動を無視する」こと。
> 

### 動作確認

下記をそれぞれ実行する。

- PowerShell

```html
docker --version
docker run hello-world
```

- Ubuntu

```html
docker --version
sudo docker run hello-world
```

それぞれ”Hello from Docker!”の文字列が表示されていれば問題なし。

## Laravel のインストール

UbuntuでPHP・Composer・Laravel installerをまとめて入れる。  
（Laravel公式は Linux 向けにこのワンライナーを出しているので一括で入る）

```html
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

その後新しいプロジェクトを作成。

```html
cd ~/projects
laravel new test_pj
```

起動できればOK。

```html
cd test_pj
php artisan serve
```

## Sail のセットアップ

依存をDocker側で入れ直すためリセットする。

```
rm -rf vendor composer.lock
```

### Sail を Docker に入れ直す

Dockerコンテナ内で Composer を実行する。
（Sailの公式リポジトリでも、Sailは Docker powered local development experience とされてる。）

```
docker run --rm \
-u"$(id -u):$(id -g)" \
-v"$PWD":/var/www/html \
-w /var/www/html \
  laravelsail/php84-composer:latest \
  composer install
```

#### ザックリとした説明

- `docker run` → コンテナ起動
- `-rm` → 終わったら捨てる
- `u ...` → 権限合わせ
- `v ...` → 今のフォルダを共有
- `w ...` → そのフォルダで作業開始
- `laravelsail/php84-composer:latest` → PHP 8.4 + Composer入りの箱
- `composer install` → 依存インストール

### Sail の設定ファイルを生成

```html
docker run --rm \
-u "$(id -u):$(id -g)" \
-v "$PWD":/var/www/html \
-w /var/www/html \
laravelsail/php84-composer:latest \
php artisan sail:install --with=pgsql,redis,mailpit
```

※”--with=”以降は必要に応じて変更。

（最後の行を&&でつなげて実行することもできるが、エラー調査のために別途行う）

### Sail を起動

ビルド

```html
./vendor/bin/sail build --no-cache
```

バージョン確認

```
./vendor/bin/sail artisan --version
```

バックグラウンドで起動する。localhostで表示されればOK.

```html
./vendor/bin/sail up -d
```

## alias設定

Ubuntuで「./vendor/bin/sail 〇〇」を「sail 〇〇」で実行できるようにする。

専用フォルダでファイルを分けると管理しやすいので今回はそのやり方で行う。

```html
mkdir -p ~/.config/shell           # エイリアスまとめフォルダ
touch ~/.config/shell/aliases.sh   # 単純な alias
touch ~/.config/shell/functions.sh # 関数
touch ~/.config/shell/git.sh       # Git系
touch ~/.config/shell/laravel.sh   # Sail / Artisan系
```

- ~/.config/shell/laravel.sh

```bash
sail() {
	if [ -f sail ]; then
		bash sail "$@"
	else
		bash vendor/bin/sail "$@"
	fi
}
```

- ~/.bashrc

```bash
# Load custom aliases and functions
for file in ~/.config/shell/*.sh; do
  [ -f "$file" ] && source "$file"
done
```

反映

```bash
source ~/.bashrc
```