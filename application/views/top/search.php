<base href="<?= base_url(); ?>">
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" media="all" />

<style>
svg line {
    /*stroke: blue;*/
    stroke-width: 1;
}

svg text.init {
    font-family: sans-serif;
    font-weight: normal;
}

svg circle.init {
    cursor: pointer;
    fill: white;
    stroke: gray;
    stroke-width: 2;
}

body {
    text-align: center;
    width: 100%;
}

.overlay {
    fill: WhiteSmoke;
    pointer-events: all;
}

div#container {
    width: 100%;
    margin-left:auto;
    margin-right:auto;
}

div#map {
    float: left;
}

div#leftbar {
    position: absolute;
    left: 0;
    margin-top: 10px;
    margin-right: 40px;
}

div#rightbar {
    position: absolute;
    right: 0;
    margin-top: 10px;
    margin-right: 40px;
}

</style>

<body>

<div id="container">
    <div id="map"></div>

    <div id="leftbar">
        <div class="well well-large">
            <h5>別の言葉で検索する</h5>
            <form  method="POST" action="top/search">
                <input class="input-large search-query" type="text" name="trgt">
                <input class="btn btn-mini btn-inverse" type="submit" name="btn1" value="search">
            </form>
        </div>
    </div>

    <div id="rightbar">
        <div class="well well-large">
            <h5> 検索タイプ </h5>
            <div class="btn-group searchType" data-toggle="buttons-radio">
                <button type="button" class="btn btn-primary active" value="related">関連キーワード</button>
                <button type="button" class="btn btn-danger" value="analize">形態素解析</button>
            </div>
        </div>
        <div class="well well-large">
            <h5>文字の大きさ </h5>
            <button class="btn btn-warning" onClick="setSize('literal', 'minusLarge')"><<</button>
            <button class="btn btn-info" onClick="setSize('literal', 'minus')"><</button>
            <span id="font_size" class="badge badge-inverse">15</span>
            <button  class="btn btn-info" onClick="setSize('literal', 'plus')">></button>
            <button  class="btn btn-warning" onClick="setSize('literal', 'plusLarge')">>></button>
        </div>
        <div class="well well-large">
            <h5> ○の大きさ </h5>
            <button  class="btn btn-warning" onClick="setSize('circle', 'minusLarge')"><<</button>
            <button  class="btn btn-info" onClick="setSize('circle', 'minus')"><</button>
            <span id="circle_size" class="badge badge-inverse">10</span>
            <button  class="btn btn-info" onClick="setSize('circle', 'plus')">></button>
            <button  class="btn btn-warning" onClick="setSize('circle', 'plusLarge')">>></button>
        </div>
    </div>
</div>

<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
    console.log($(".searchType > .active").val());
    // init
    var list = {
        nodes : [
            { name : "<?= $trgt ?>" },
        ],
        links : [
        ]
    };

    // svg
    var w = window.innerWidth;
    var h = window.innerHeight;

    var label_dist = 7;

    var svg = d3.select("#map").append("svg")
        .attr("width", w).attr("height", h)
        .append("g")
        .call(d3.behavior.zoom().scaleExtent([0, 8]).on("zoom", zoom))
        .append("g");
    
    var force = d3.layout.force()
        .nodes(list.nodes)
        .links(list.links)
        .charge(-500)
        .gravity(0.1)
        .size([w, h])
        .linkDistance(80)
        .on("tick", tick);

    var link      = svg.selectAll("line").data(list.links);
    var node      = svg.selectAll("circle").data(list.nodes);
    var label     = svg.selectAll("text").data(list.nodes);

    var color = d3.scale.category10();  // 20色を指定

    // ovarlay to drag and zoom background
    svg.append("rect")
        .attr("class", "overlay")
        .attr("width", 10000)
        .attr("height", 10000)
        .attr("transform", "translate(-4000, -4000)") // move criteria

    restart();

    // animation
    function tick() {
        link
            .attr("x1", function(d){ return d.source.x; })
            .attr("y1", function(d){ return d.source.y; })
            .attr("x2", function(d){ return d.target.x; })
            .attr("y2", function(d){ return d.target.y; });
        node
            .attr("cx", function(d){ return d.x })
            .attr("cy", function(d){ return d.y });
        label
            .attr("x", function(d){ return d.x + label_dist })
            .attr("y", function(d){ return d.y - label_dist });
    }

    // restart
    function restart() {
        // update link
        link = link.data(list.links);
        link.enter().append("line").attr("stroke", getStrokeColor());

        // update node
        node = node.data(list.nodes);
        node.enter().append("circle")
            .attr("r", function(d){
                return $("#circle_size").text() + "px";
            })
        .classed("init", true)
        .on("click", function(d){
            var keyword = d.name;
            d3.select(this)
                .classed("init", false)
                .transition()
                .duration(100)
                .attr("r", function(d){
                    return Number($("#circle_size").text()) + 2 + "px";
                })
                .transition()
                .duration(200)
                .attr("r", function(d){
                    return $("#circle_size").text() + "px";
                })
                .attr("stroke", "purple")
                .attr("stroke-width", 2)
                .attr("fill", function(d){
                    var type = $(".searchType > .active");
                    var color;
                    if (type.val() == 'related') {
                        color = "blue";
                    } else if (type.val() == 'analize') {
                        color = "red";
                    }
                    return color;
                });
            $.ajax({
                type: "POST",
                url: getSearchType(),
                data: {keyword: keyword},
                dataType: "json",

                success: function(results){
                    if (d.fixed == true) return false;
                    pushSearchResults(d, results)
                    restart();
                }
            });
        })
        .call(force.drag);

        // text
        label = label.data(list.nodes);
        label.enter().append("text")
            .text(function(d) { return d.name; })
            .classed("init", true)
            .attr("font-size", function(d){
                return $("#font_size").text() + "px";  
            })
            .style("fill", function(d, i){
                return color(i);
            })
            .on("mouseover", function(d){
                d3.select(this)
                    .classed("init", false)
                    .attr("cursor", "default") 
                    .attr("font-weight", "bold") 
                    .attr("font-size", function(d){
                        return Number($("#font_size").text()) + 2 + "px";
                    });
            })
            .on("mouseout", function(d){
                d3.select(this)
                    .classed("init", true)
                    .attr("font-size", function(d){
                        return $("#font_size").text() + "px";
                    });
            })
            .on("click", function(d){
                window.open("https://www.google.co.jp/search?q=" + d.name);
            })
            .call(force.drag);
        force.start();
    }

    function pushSearchResults(d, results) {

        d.fixed = true;
        var chk_nodes = new Object();

        // make check nodes.
        for (var i = 0; i < list.nodes.length; i++) {
            chk_nodes[list.nodes[i].name] = list.nodes[i].index;
        }

        // update nodes
        for (var i = 0; i < results.length; i++) {
            // node exists -> only push links.
            if (chk_nodes[results[i]] !== undefined) {
                list.links.push( {source : d.index, target: chk_nodes[results[i]]} );
            } else {
                // else push node and links
                list.links.push( {source : d.index, target: list.nodes.length} );
                list.nodes.push( {name : results[i]} );
            }
        }
    }

    function setSize(type, calc) {
        var min = 1;
        var max = 90;
        if (type == 'literal') {
            var font_size = $("#font_size").text();
            var new_font_size;
            switch (calc) {
                case "minusLarge":
                    new_font_size = parseInt(font_size, 10) - 5;
                break;
                case "minus":
                    new_font_size = parseInt(font_size, 10) - 1;
                break;
                case "plus":
                    new_font_size = parseInt(font_size, 10) + 1;
                break;
                case "plusLarge":
                    new_font_size = parseInt(font_size, 10) + 5;
                break;
            }
            if (new_font_size < min) {
                new_font_size = min;
            } else if (new_font_size > max) {
                new_font_size = max;
            }
            $("#font_size").text(new_font_size);
        } else if (type == 'circle') {
            var circle_size = $("#circle_size").text();
            var new_circle_size;
            switch (calc) {
                case "minusLarge":
                    new_circle_size = parseInt(circle_size, 10) - 5;
                break;
                case "minus":
                    new_circle_size = parseInt(circle_size, 10) - 1;
                break;
                case "plus":
                    new_circle_size = parseInt(circle_size, 10) + 1;
                break;
                case "plusLarge":
                    new_circle_size = parseInt(circle_size, 10) + 5;
                break;
            }
            if (new_circle_size < min) {
                new_circle_size = min;
            } else if (new_circle_size > max) {
                new_circle_size = max;
            }
            $("#circle_size").text(new_circle_size);
        }
        changeDrawSize();
    }

    // node, labelのデータを更新する
    function changeDrawSize() {
        node.data(list.nodes)
            .transition()
            .duration(200)
            .attr("r", function(d){
                return $("#circle_size").text() + "px";
            });
        label.data(list.nodes)
            .transition()
            .duration(200)
            .attr("font-size", function(d){
                return $("#font_size").text() + "px";  
            });
    }

    function getSearchType() {
        var type = $(".searchType > .active");
        var url;
        if (type.val() == 'related') {
            url = "<?= base_url(); ?>ajax/related";
        } else if (type.val() == 'analize') {
            url = "<?= base_url(); ?>ajax/happy";
        }
        return url;
    }

    function getStrokeColor() {
        var type = $(".searchType > .active");
        var color;
        if (type.val() == 'related') {
            color = "blue";
        } else if (type.val() == 'analize') {
            color = "red";
        }
        return color;
    }

    function zoom() {
        svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }
</script>

</body>
