<base href="<?= base_url(); ?>">

<style>
svg line {
    stroke: blue;
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

div.set_center {
    text-align: center;
    margin: 0 auto;
}

.overlay {
    fill: WhiteSmoke;
    pointer-events: all;
}

</style>

<body>

<p> 文字の大きさ
<button onClick="setSize('literal', 'minusLarge')"><<</button>
<button onClick="setSi0e('literal', 'minus')"><</button>
<span id="font_size">15</span>px
<button onClick="setSize('literal', 'plus')">></button>
<button onClick="setSize('literal', 'plusLarge')">>></button>
</p>

<p> ○の大きさ
<button onClick="setSize('circle', 'minusLarge')"><<</button>
<button onClick="setSize('circle', 'minus')"><</button>
<span id="circle_size">10</span>px
<button onClick="setSize('circle', 'plus')">></button>
<button onClick="setSize('circle', 'plusLarge')">>></button>
</p>

<p>
<input type="radio" name="searchType" value="related" checked="checked">関連キーワード
<input type="radio" name="searchType" value="analize">形態素解析
</p>

<div class="set_center"></div>

<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
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

    var svg = d3.select("div").append("svg")
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

    // drag @途中
    var link      = svg.selectAll("line").data(list.links);
    var node      = svg.selectAll("circle").data(list.nodes);
    var label     = svg.selectAll("text").data(list.nodes);

    var color = d3.scale.category10();  // 20色を指定

    // ovarlay to drag and zoom background
    svg.append("rect")
        .attr("class", "overlay")
        .attr("width", 5000)
        .attr("height", 5000)
        .attr("transform", "translate(-1500, -2000)") // move criteria

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
        link.enter().append("line");

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
                    var type = $("input[name='searchType']:checked");
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
        var type = $("input[name='searchType']:checked");
        var url;
        if (type.val() == 'related') {
            url = "<?= base_url(); ?>ajax/related";
        } else if (type.val() == 'analize') {
            url = "<?= base_url(); ?>ajax/happy";
        }
        return url;
    }

    function zoom() {
        svg.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }
</script>

</body>
