<base href="<?= base_url(); ?>">

<body>
    <button id="0">add</button>
<br />

<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
    // init
    var list = {
        nodes : [
            { name : "a" },
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

    $(function(){
        $('button').on('click', function(){
            var button_id = $(this).attr("id");

            $.ajax({
                type: "POST",
                url: "<?= base_url(); ?>ajax",
                data: {number: button_id},
                dataType: "json",

                success: function(data){
                    update();
                }
            });
        });
    });

    var force = d3.layout.force()
        .nodes(list.nodes)
        .links(list.links)
        .charge(-500)
        .gravity(0.1)
        .size([w, h])
        .linkDistance(100)
        .on("tick", tick)
        .start();

    var link      = svg.selectAll("line").data(list.links)
                    .enter().append("line")
                    .style("stroke", "red")
                    .style("stroke-width", 1);

    var node      = svg.selectAll("circle").data(list.nodes)
                    .enter().append("circle")
                    .attr("r", 10)
                    .call(force.drag);

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
    }

    // update
    function update() {
        pushElement();

        // update link
        link = link.data(list.links);
        link.enter().append("line")
        .style("stroke", "red")
        .style("stroke-width", 1);

        // update node
        node = node.data(list.nodes);
        node.enter().append("circle")
        .attr("r", 10)
        .call(force.drag);

        force.start();

    }

    function pushElement() {

        list.links.push( {source : 0, target: list.nodes.length} );
        list.nodes.push( {name : "c"} );

    }

</script>

</body>
