
<style>

    div.bar {
        display: inline-block;
        width: 20px;
        height: 75px;
        margin-right: 1px;
        background-color: teal;
    }

    /* svg */

    svg .pumpkin {
        fill: yellow;
        stroke: orange;
        stroke-width: 2;
        opacity: 0.5;
    }  

</style>

<body>

<!--
<svg width="500" height="50">
    <rect x="0" y="0" width="500" height="50"> 
        <circle cx="25" cy="25" r="22" class="pumpkin">

</svg>
-->

<br />



<script src="http://d3js.org/d3.v3.min.js"></script>
    <script>

        // object
        /*
        var fruits = {
                kind: "hgoe",
                color: "red",
                tasty: true,
                quantity: 12
        };

        console.log(fruits);
        console.log(fruits.kind);
        console.log(fruits.tasty);

        // array & object
        var fruits = [
            {
                kind: "hgoe1",
                color: "red1",
                tasty: true,
                quantity: 1
            },
            {
                kind: "hgoe2",
                color: "red2",
                tasty: true,
                quantity: 2
            },
            {
                kind: "hgoe3",
                color: "red3",
                tasty: true,
                quantity: 3
            }
        ];

        console.log(fruits)
        console.log(fruits[0].color);
        console.log(fruits[1].color);
        console.log(fruits[2].quantity);

        var jsonFruit = {
                "kind": "hoge",
                "some" : "hoge2"
            };

        console.log(jsonFruit.kind);
         */

        


       //var dataset = [5, 10, 15, 8, 20, 25];

        var dataset = [];
        for (var i = 0; i < 25; i++) {
            dataset.push(Math.ceil(Math.random() * 20));
        }
/* svg circle sample
        var w = dataset.length * 70;
        var h = 100;

        // define svg
        var svg = d3.select("body")
                    .append("svg")
                    .attr("width", w)
                    .attr("height", h);

        // draw
        var circles = svg.selectAll("circle").data(dataset).enter().append("circle")

        // define circles
        circles.attr("cx", function(d, i) { return (i * 50) + 25;})
                    .attr("cy", h/2)
                    .attr("r", function(d) { return d; })
                    .attr("class", "pumpkin");
 */

        /* svg rect sample */
        var w = 500;
        var h = 100;
        var padding = 1;

        var svg = d3.select("body").append("svg")
                    .attr("width", w).attr("height", h * 2);

        // rect
        svg.selectAll("rect").data(dataset)
            .enter().append("rect")
            .attr("x", function(d, i) {
                return i * (w / dataset.length);
            })
            .attr("y", function(d) {
                return h - d * 4;
            })
            .attr("width", w / dataset.length - padding)
            .attr("height", function(d) {
                return d * 4;
            })
            .attr("fill", function(d){
               return "rgb(0, 0, " + (d * 10) +")";
            });

        // text
        svg.selectAll("text").data(dataset)
            .enter().append("text")
            .text(function(d) { return d; })
            .attr("x", function(d, i) {
                return i * (w / dataset.length) + (w / dataset.length - padding) / 2;
            })
            .attr("y", function(d) {
                return h + 12;
            })
            .attr("font-family", "sans-serif")
            .attr("font-size", "11px")
            .attr("text-anchor", "middle");

        /*
        d3.select("body").selectAll("div")
            .data(dataset)
            .enter()
            .append("div")
            .attr("class", "bar")
            .style("height", function(d){
                return d * 5 + "px";
            });
        */

        // div sample
        /*
        d3.select("body").selectAll("div").data(dataset).enter().append("div").attr("class", "bar")
            .style("height", function(d){ 
                return d * 5 + "px"; 
            })
        */
       /* 
        d3.select("body").data(dataset).enter().append("p").text(function(d) { return d; })
            .style("color", function(d){
                if (d > 15) {
                    return "red"
                } else {
                    return "black"
                }
            });
        */
    </script>
</body>






<!--
<style>

.axis {
  font: 10px sans-serif;
  -webkit-user-select: none;
  -moz-user-select: none;
  user-select: none;
}

.axis .domain {
  fill: none;
  stroke: #000;
  stroke-opacity: .3;
  stroke-width: 10px;
  stroke-linecap: round;
}

.axis .halo {
  fill: none;
  stroke: #ddd;
  stroke-width: 8px;
  stroke-linecap: round;
}

.slider .handle {
  fill: #fff;
  stroke: #000;
  stroke-opacity: .5;
  stroke-width: 1.25px;
  pointer-events: none;
}

</style>
<body>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script>

var margin = {top: 200, right: 50, bottom: 200, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.bottom - margin.top;

var x = d3.scale.linear()
    .domain([0, 180])
    .range([0, width])
    .clamp(true);

var brush = d3.svg.brush()
    .x(x)
    .extent([0, 0])
    .on("brush", brushed);

var svg = d3.select("body").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

svg.append("g")
    .attr("class", "x axis")
    .attr("transform", "translate(0," + height / 2 + ")")
    .call(d3.svg.axis()
      .scale(x)
      .orient("bottom")
      .tickFormat(function(d) { return d + "°"; })
      .tickSize(0)
      .tickPadding(12))
  .select(".domain")
  .select(function() { return this.parentNode.appendChild(this.cloneNode(true)); })
    .attr("class", "halo");

var slider = svg.append("g")
    .attr("class", "slider")
    .call(brush);

slider.selectAll(".extent,.resize")
    .remove();

slider.select(".background")
    .attr("height", height);

var handle = slider.append("circle")
    .attr("class", "handle")
    .attr("transform", "translate(0," + height / 2 + ")")
    .attr("r", 9);

slider
    .call(brush.event)
  .transition() // gratuitous intro!
    .duration(750)
    .call(brush.extent([70, 70]))
    .call(brush.event);

function brushed() {
  var value = brush.extent()[0];

  if (d3.event.sourceEvent) { // not a programmatic event
    value = x.invert(d3.mouse(this)[0]);
    brush.extent([value, value]);
  }

  handle.attr("cx", x(value));
  d3.select("body").style("background-color", d3.hsl(value, .8, .8));
}

</script>
-->
