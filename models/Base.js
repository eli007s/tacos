const Joi = require('@hapi/joi');

module.exports = class BaseModel {

  constructor(schema){
    this.schema = schema;
  }
  
  validate(obj, cb) {
    return Joi.validate(obj, this.schema, cb);
  }
}
