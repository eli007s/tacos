const express = require('express'),
  bodyParser = require('body-parser'),
  routes = require('./routes'),
  port = 8080,
  app = express();

app.use(bodyParser.json());

routes(app);

app.listen(port, () => console.log(`listening on ${port}`));