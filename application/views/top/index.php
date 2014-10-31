<base href="<?= base_url(); ?>">

<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all" />

<style>
body {
    text-align: center;
    width: 100%;
}
div#container {
    text-align: center;
    margin:0 auto;
    width: 720px;
}

h3 {
    margin-top: 20px;
    margin-bottom: 20px;
}
form {
    margin-top: 50px;
}


</style>

<body>

<div id="container">

    <h3>キーワード検索</h3>

    <p class="text-info">○をクリックすると、さらに検索します。</p>
    <p class="text-info">textをクリックすると、検索結果ページを表示します。</p>

    <p>検索タイプは<font color="red">関連キーワード</font>か<font color="red">形態素解析</font>を選べます。</p>
    <p>形態素解析を選ぶと検索結果1位のものの単語分析を行います。</p>

    <form  method="POST" action="top/search">
        <input class="input-large search-query" type="text" name="trgt">
        <input class="btn btn-primary" type="submit" name="btn1" value="search">
    </form>

</div>

</body>
