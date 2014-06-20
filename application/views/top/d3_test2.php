<base href="<?= base_url(); ?>">

<body>

    <button id="0">button 0</button>
    <button id="1">button 1</button>

<?= base_url(); ?>

<br />


<script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<script src="http://d3js.org/d3.v3.min.js"></script>

<script>
    var w = 500;
    var h = 300;

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
  //                  draw(data);
                }
            });

        });
    });

    var list = {
        nodes : [
            { name : "a" },
            { name : "b" },
            { name : "c" }
        ],
        links : [
            { source : 0, target : 1 },
            { source : 0, target : 2 },
            //{ source : 1, target : 2 }
        ]
    };

    var force = d3.layout.force()
        .nodes(list.nodes)
        .links(list.links)
        .size([w, h])
        .linkDistance(100)
        .start();

    var link = svg.selectAll("line").data(list.links).enter().append("line")
                    .style("stroke", "red")
                    .style("stroke-width", 5);

    var node = svg.selectAll("circle").data(list.nodes).enter().append("circle")
                    .attr("r", 10)
                    .call(force.drag);

    force.on("tick", function(){
        link
        .attr("x1", function(d){ return d.source.x; })
        .attr("y1", function(d){ return d.source.y; })
        .attr("x2", function(d){ return d.target.x; })
        .attr("y2", function(d){ return d.target.y; });
        node
        .attr("cx", function(d){ return d.x })
        .attr("cy", function(d){ return d.y });
    });


    


</script>



</body>
