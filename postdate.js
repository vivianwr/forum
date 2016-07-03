var http = require("http");
var express = require('express');
var bodyParser = require('body-parser');
console.log("Started server");

var aRouter = express();
var myServer = http.createServer(aRouter);
aRouter.use(bodyParser.json());
aRouter.use(bodyParser.urlencoded({ extended: true }));
aRouter.use(express.static(__dirname + "/client"));

aRouter.get('/users/:userid', function(req, res){
   res.send("You asked for data from User "+req.params.userid); 
});

aRouter.post('/users', function(req, res){
   res.json(req.body);
});

aRouter.get("/*", function(req, res){
   res.send("Not found"); 
});

myServer.listen(8080, '0.0.0.0');