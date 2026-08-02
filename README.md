# HOUSEHOLD BUDGET

締め日を基準に、日別・月別の支出状況と予算超過を
ひと目で確認できる家計簿Webアプリケーションです。

支出を記録するだけでなく、
「今日は使いすぎていないか」
「今月の予算に対してどの位置にいるか」
を視覚的に把握できることを目的として開発しています。

## Demo

[デモサイトを開く](https://web--household-budget--b74twz2xwxtt.code.run/login)

> 無料ホスティングを使用しているため、
> 初回表示に時間がかかる場合があります。

### Demo Account
「Sign in」ボタンの下に「Tery the demo account」というボタンがあります。
こちらは後述の「UI設計・画面仕様書」を再現したデータが既に入っており、更にデータを追加することも可能です。
ただし以下の行為は禁止しており、定期的にデータを初期化することがあります。
- メールアドレス変更
- パスワード変更
- アカウント削除

## Screenshot

![Dashboard](docs/images/dashboard.png)

## Features

- ユーザー登録・ログイン
- 支出の登録・編集・削除
- 月間カレンダーによる支出状況の表示
- 日別・月別の支出分析
- 月間予算・支出上限・締め日の設定
- カテゴリの追加・色・状態の管理
- 表示色や判定基準のカスタマイズ
- 定期支出(サブスクリプション)の管理

## Technologies

| Category | Technology | Purpose |
|---|---|---|
| Backend | PHP / Laravel | 認証、バリデーション、データ処理 |
| Frontend | JavaScript | 画面操作、カレンダー、UI制御 |
| Styling | SCSS | スタイルの分割と共通化 |
| Database | PostgreSQL | 支出・予算・設定データの管理 |
| Development | Docker / Laravel Sail | 開発環境の統一 |
| Build | Vite | JavaScript・SCSSのビルド |
| Deployment | Northflank | Webアプリケーションの公開 |

## Design and Implementation

### ユーザーごとのデータ分離

ログイン中のユーザーが、自分の支出やカテゴリだけを
操作できるように実装しています。

### データベースの整合性

外部キーや一意制約、CHECK制約を使用し、
不正なデータが保存されにくい構造にしています。

### UI・UX

日別・月別の支出状態を色で確認できるようにし、
家計簿を毎日確認するときの負担を減らしています。

SCSSは役割ごとに分割し、フォームやカードなどの
共通コンポーネントを再利用しています。

### バリデーションとセキュリティ

Laravelのバリデーションを使用して入力内容を確認し、
認証と認可を区別してデータを保護しています。

## Documents

- [環境構築手順](docs/setup.md)
- [UI設計・画面仕様書](docs/ui-design.pdf)

※実際に存在する資料だけを掲載します。

## Setup

詳しい環境構築手順は
[docs/setup.md](docs/setup.md) を参照してください。

---

Naho Taniguchi
