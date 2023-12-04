const express = require("express");
const bodyParser = require("body-parser");
const router = express.Router();
const amazon = require("./amazon.route");
const yahoo = require("./yahoo.route");

const initializeRoute = (app) => {
	router.use('/amazon', amazon);
	router.use('/yahoo', yahoo);
	app.use('/api/v1', router);
	app.use(bodyParser.json({
		limit: '100mb'
	}));
	app.use(bodyParser.urlencoded({
		limit: '100mb',
		extended: true,
		parameterLimit: 1000000
	}));
}

module.exports = initializeRoute;