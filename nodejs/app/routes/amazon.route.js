const express = require("express");
const router = express.Router();
const amazon = require("../controllers/amazon.controller.js");

router.post("/product_register", amazon.getInfo);

module.exports = router;
