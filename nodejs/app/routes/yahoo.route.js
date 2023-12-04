const express = require("express");
const router = express.Router();
// const yahoo_token = require("../controllers/yahoo.token.js");
const yahoo_order = require("../controllers/yahoo.order.js");
const yahoo_exhibit = require("../controllers/yahoo.exhibit.js");


// router.post('/new_authorization', yahoo_token.newAuthorization);
// router.post('/re_authorization', yahoo_token.reAuthorization);


router.post('/get_order', yahoo_order.yahoo_orderCount);
router.post('/product_exhibit', yahoo_exhibit.exhibit);


module.exports = router;