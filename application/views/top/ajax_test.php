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
                    draw(data);
                }
            });

        });
    });

    function draw(input) {

        console.log(input);

        radius = 0;
        color = "black";
        if (input == 0) {
            radius = 50;
            color = "red";
        } else {
            radius = 100;
            color = "blue";
        }

        console.log("radius : " + radius);

        var circles = svg.selectAll("circle").data(input).enter().append("circle")
                        .attr("cx", 0)
                        .attr("cy", 0)
                        .attr("r", radius)
                        .attr("fill", color)
                        .on("mouseover", function(){
                            d3.select(this)
                            .transition().delay(500).duration(1000)    
                            .attr("fill", "green");
                        })
                        .on("mouseout", function(){
                            d3.select(this).attr("fill", color);
                        })
                        .on("click", function(){
                            console.log("click done!");
                        
                        });
       
    };

</script>



</body>
