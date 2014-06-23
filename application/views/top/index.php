<base href="<?= base_url(); ?>">

<style>



</style>

<body>





<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
    // init
    var list = {
        nodes : [
            { name : "aaaaaaa" },
            { name : "b" },
        ],
        links : [
            { source : 0, target : 1 },
        ]
    };

    var w = 500;
    var h = 500;

    var svg = d3.select("body").append("svg")
        .attr("width", w).attr("height", h);

    var force = d3.layout.force()
        .nodes(list.nodes)
        .links(list.links)
        .charge(-500)
        .gravity(0.1)
        .size([w, h])
        .linkDistance(100)
        .on("tick", tick)

    var link      = svg.selectAll("line").data(list.links);
    var node      = svg.selectAll("circle").data(list.nodes);
    var label     = svg.selectAll("text").data(list.nodes);
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
            .attr("x", function(d){ return d.x })
            .attr("y", function(d){ return d.y });

    }

    // restart
    function restart() {
        // update link
        link = link.data(list.links);
        link.enter().append("line")
        .style("stroke", "red")
        .style("stroke-width", 1);

        // update node
        node = node.data(list.nodes);
        node.enter().append("circle")
        .attr("r", 10)
        .on("mousedown", function(d){
            var keyword = d.name;

            $.ajax({
                type: "POST",
                url: "<?= base_url(); ?>ajax",
                data: {keyword: keyword},
                dataType: "json",

                success: function(data){
                    console.log(data);
                }
            });

            pushElement(d.index);
            restart();
        })
        .call(force.drag);

        // text
        label = label.data(list.nodes);
        label.enter().append("text")
            .attr("font-family", "sans-serif")
            .attr("fill", "green")
            .attr("font-weight", "bold")
            .text(function(d) { return d.name; })
            .call(force.drag);
        force.start();
    }

    function pushElement(index) {
        list.links.push( {source : index, target: list.nodes.length} );
        list.nodes.push( {name : "c"} );
    }

</script>

</body>
