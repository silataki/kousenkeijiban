高専掲示板を作ろう!!!!!!!!!!!!!!!!!!!
pushするときの名前などのルールは後で決めます^_^

githubの中にはmainとbranchが存在します！
mainは本番用・完成版，branchは作業用・開発用の役割をします
完成版のmainをみんなが編集してごちゃごちゃになっちゃうといけないので，それぞれのbranchで開発してもらった後，mainに合流（マージ）させます！
branchの名前をみて，自分が担当するbranchに移動した後に作業を開始してください！

vscodeでプログラムを書いて保存しただけでは自分のPCにしか反映されていないので，コマンドを使ってgithubにも変更を反映させる必要があります！
githubに変更を反映させることはゲームのセーブと同じで，ある程度進んだらセーブという感じで自分のタイミングでしてください！

＜＜開発の流れ＞＞
powershellやcommandpromptをubuntuで開いて，コマンドでvscodeに移動
↓
コマンドで自分が担当するbranchに移動
↓
プログラミングしたあとローカルに保存
↓
git（リモート）に変更を上げる
↓（自分が担当する部分の実装が完了したら）
webのgithubに移動してpull requestをだす
↓
pull requestを出したことを連絡する

＜＜vscodeでapacheを使ってphpをブラウザ上で実行する方法＞＞
vscodeのターミナルで実行
1. sudo apt update
2. sudo apt install apache2 php libapache2-mod-php
3. （ブラウザで）http://localhost
  　 ⇒「apache2 ubuntu default page」などと表示されればOK！
4. sudo cp 実行したいファイルのパス /var/www/html/
     例）sudo cp ~/kousenkeijiban/src/top_page.php /var/www/html/
5. (ブラウザで)http://localhost/ファイル名/
     例）http://localhost/top_page.php

＜＜コマンド集＞＞
code .　　　　　　　　　　　　　　　　powershellやcommandpromptからvscodeに移動するコマンド
mkdir ディレクトリ名                今いるディレクトリの下に新たにディレクトリを作成するコマンド
git branch　　　　　　　　　　　　　　今自分がどのbranchにいるのか確認するコマンド
git checkout ブランチ名　　　　　　　指定したbranchに移動するコマンド
git clone                          リモートをコピーする
git pull origin main　　　　　　　　mainに変更があった時にそれを今自分がいるbranchに反映させるコマンド　　　　　　　　　
 | git add .　　　　　　　　　　　　 変更・追加・削除したファイルをgit（リモート）に登録するコマンド　「.」はすべてのファイルという意味　
 | git commit -m "何をしたの説明"　 addで登録した内容を「1つの記録」として保存する　””のなかには「ログイン画面の追加」などこの変更で何をしたのか簡潔に書く
 | git push origin ブランチ名　　　　指定したリモート上のbranchにローカルでの変更を反映させるコマンド
※「|」でまとめてある3つのコマンドはリモートに上げるときにセットで使う

リポジトリ：プロジェクトのフォルダ＋変更の履歴を全部まとめて管理する場所
ローカル：自分のPC内にあるgitリポジトリ　　リモート：githubなどインターネット上にある共有のgitリポジトリ
