const Base = require('./Base'),
  Joi = require('@hapi/joi'),
  fs = require('fs'),
  path = require('path'),
  dbPath = path.resolve(__dirname, '..', 'db.json');

class Taco extends Base {
  constructor() {
    super({
      name: Joi.string().max(255),
      tortilla: Joi.string(),
      toppings: Joi.string(),
      vegetarian: Joi.boolean(),
      soft: Joi.boolean()
    });
  }

  updateTaco(tacoData) {
    let { tacos } = JSON.parse(fs.readFileSync(dbPath, 'utf-8'));
    for(let i = 0; i < tacos.length; i++) {
      if(tacos[i].name == tacoData.name) {
        tacos[i] = tacoData;
        break;
      }
    }
    fs.writeFileSync(dbPath, JSON.stringify({tacos}));
    return 'done';
  }

  removeTaco(name) {
    let { tacos } = JSON.parse(fs.readFileSync(dbPath, 'utf-8'));
    for(let i = 0; i < tacos.length; i++) {
      if(tacos[i].name == name) {
        tacos.splice(i, 1)
        break;
      }
    }
    fs.writeFileSync(dbPath, JSON.stringify({tacos}));
    return 'done';
  }

  getTaco(name) {
    const { tacos } = JSON.parse(fs.readFileSync(dbPath, 'utf-8'));
    for(let i = 0; i < tacos.length; i++) {
      if(tacos[i].name === name) {
        return tacos[i];
      }
    }
    return 'not found';
  }

  getTacos() {
    const { tacos } = JSON.parse(fs.readFileSync(dbPath, 'utf-8'));
    return tacos;
  }
    
}

module.exports = new Taco();
