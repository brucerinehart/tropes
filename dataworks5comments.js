
	var w = 1200,
	    h = 600,
	    radius = d3.scale.log().domain([0, 312000]).range(["10", "50"]);
	    trangle = d3.svg.symbol().type("triangle-up");
	
	var color = d3.scale.category20();
	var data = dadata;
	var vis = d3.select("body").append("svg:svg")
	    .attr("width", w)
	    .attr("height", h);

	vis.append("defs").append("marker")
	    .attr("id", "arrowhead")
	    .attr("refX", 17 + 3) /*must be smarter way to calculate shift*/
	.attr("refY", 2)
	    .attr("markerWidth", 6)
	    .attr("markerHeight", 4)
	    .attr("orient", "auto")
	    .append("path")
	    .attr("d", "M 0,0 V 4 L6,2 Z"); //this is actual shape for arrowhead



console.log(dadata);

// prolly nuthin

// node indexing stuff

dadata.nodes.forEach(function(nod){
nod.nodeID = dadata.nodes.indexOf(nod);
});


var edges = [];
dadata.links.forEach(function(e) {
  var sourceNode, targetNode;
  dadata.nodes.forEach(function(n) {
    // baking in making links to docs...
    //just people
    // if link source

    if (e.source == n.name){
    sourceNode = n.nodeID;
    };
  
    if (e.target == n.name){
      targetNode = n.nodeID;
    };
  });

    edges.push({
        source: sourceNode,
        target: targetNode
        //value: e.Value
    });
});




//d3.json("dadata2fromWP.json", function(data) {
	var force = self.force = d3.layout.force()
	    .nodes(dadata.nodes)
	
	//  styling from before
	    // .links(data.links)
	    // .distance(480)
	    // .charge(-110)
	    // .size([w, h])
	    .links(edges)
	    //.distance(120)
	    .charge(-700)
	    .size([w, h])
	    .start();


// need to fix the link calc (this code from elsewher)
	var link = vis.selectAll("line.link")
	    .data(edges)
	    .enter().append("svg:line")
	    .attr("class", "link1")
	
	// function (d) {
	// 			if (d.value == "professional"){
	// 	    return "link";}
	// 			else if (d.value == "colleague"){
	// 		return "link1";
	// 			}
	// 	})
	    .attr("x1", function (d) {
	    return d.source.x;
	})
	    .attr("y1", function (d) {
	    return d.source.y;
	})
	    .attr("x2", function (d) {
	    return d.target.x;
	})
	    .attr("y2", function (d) {
	    return d.target.y;
	})
	    .attr("marker-end", function (d) {
	    if (d.value == 1) {
	        return "url(#arrowhead)"
	    } else {
	        return " "
	    };
	});


	function openLink() {
	    return function (d) {
	        var url = "";
	        if (d.slug != "") {
	            url = d.slug
	        } //else if(d.type == 2) {
	        //url = "clients/" + d.slug
	        //} else if(d.type == 3) {
	        //url = "agencies/" + d.slug
	        //}
	        window.open("http://" + url)
//	        window.open(url)
	    }
	}




	var node = vis.selectAll("g.node")
	    .data(dadata.nodes)
	    .enter().append("svg:g")
        .style("fill", function(d) { return color(d.group); })
	    .attr("class", function (d) {
	        if (d.group === "posts") {
	           return "post node";
	        } else if (d.group === "comment") {
	           return "comment node";
	        } else if (d.group === "author") {
	           return "authors circle node";
	        } else {
	           return "circle node";
	        }
	    })
	    .call(force.drag);
	// post shape
	d3.selectAll(".post").append("rect")
	    .attr("width", 15)
	    .attr("height", 30)
	    .attr("class", function (d) {
	    return "node ";
	}).on("click", openLink());
	
	// comment shape
	d3.selectAll(".comment").append("rect")
	    .attr("width", 30)
	    .attr("height", 10)
	    .attr("class", function (d) {
	    return "node ";
	}).on("click", openLink());
//	d3.selectAll(".mtg").append("trangle")

    d3.selectAll(".author").append("path")
	    .attr("class", "point")
	    .attr("d", trangle.size(260))
	    .attr("class", function (d) {
	    return "node " + d.nodeType;
	}).on("click", openLink());

	d3.selectAll(".circle").append("a")
	    .attr("xlink:href", function(d) {return "http://" + d.slug})
	    .append("circle")
	    .attr("class", function (d) {
	    return "node " + d.nodeType;
	})
	    .attr("r", function (d) {
	    return 15
	})
	//.style("fill", function(d) { return fill(d.type); })
	.call(force.drag);


	node.append("svg:text")
	    .attr("class", "nodetext")
	    .attr("dx", "1.5em")
	    .attr("dy", "1.7em")
	    .attr("text-anchor", "left")
	    .text(function (d) {
	    return d.name
	});

	force.on("tick", function () {
	    link.attr("x1", function (d) {
	        return d.source.x;
	    })
	        .attr("y1", function (d) {
	        return d.source.y;
	    })
	        .attr("x2", function (d) {
	        return d.target.x;
	    })
	        .attr("y2", function (d) {
	        return d.target.y;
	    });

	    node.attr("transform", function (d) {
	        return "translate(" + d.x + "," + d.y + ")";
	    });
	
	
	});
