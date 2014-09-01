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


</style>

<body>

<select id="font_size">
    <option value="10">10</option>
    <option value="15">15</option>
    <option value="20">20</option>
    <option value="25">25</option>
    <option value="30">30</option>
    <option value="35">35</option>
    <option value="40">40</option>
</select>

<button onclick=changeFontSize()>fontサイズ変更</button>

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

    var circle_r = 7;
    var label_dist = 7;

    var svg = d3.select("div").append("svg")
        .attr("width", w).attr("height", h);
    
    var force = d3.layout.force()
        .nodes(list.nodes)
        .links(list.links)
        .charge(-500)
        .gravity(0.1)
        .size([w, h])
        .linkDistance(60)
        .on("tick", tick)

    var link      = svg.selectAll("line").data(list.links);
    var node      = svg.selectAll("circle").data(list.nodes);
    var label     = svg.selectAll("text").data(list.nodes);

    var color = d3.scale.category10();  // 20色を指定

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
        .attr("r", 7)
        .classed("init", true)
        .on("click", function(d){
            var keyword = d.name;
            d3.select(this)
                .classed("init", false)
                .transition()
                .duration(100)
                .attr("r", 9)
                .transition()
                .duration(200)
                .attr("r", 8)
                .attr("stroke", "purple")
                .attr("stroke-width", 2)
                .attr("fill", "blue");

            $.ajax({
                type: "POST",
                url: "<?= base_url(); ?>ajax",
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
                return $("#font_size").val() + "px";  
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
                        return Number($("#font_size").val()) + 2 + "px";
                    });
            })
            .on("mouseout", function(d){
                d3.select(this)
                    .classed("init", true)
                    .attr("font-size", function(d){
                        return $("#font_size").val() + "px";
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

    function changeFontSize() {
        label.data(list.nodes)
            .transition()
            .duration(200)
            .attr("font-size", function(d){
                return $("#font_size").val() + "px";  
            });
    }
</script>

</body>
