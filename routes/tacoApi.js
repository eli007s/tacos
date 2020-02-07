const Taco = require('../models/Taco.js');

module.exports = app => {

  app.get('/api/tacos', (req, res) => {
    const tacos = Taco.getTacos()
    res.json(tacos);
  });

  app.get('/api/taco/:name', (req, res) => {
    const taco = Taco.getTaco(req.params.name)
    res.json(taco);
  });

  app.put('/api/taco/:name', (req, res) => {
    const result = Taco.updateTaco(req.body)
    res.json(result);
  });

  app.delete('/api/taco/:name', (req, res) => {
    const result = Taco.removeTaco(req.params.name)
    res.json(result);
  });

};
