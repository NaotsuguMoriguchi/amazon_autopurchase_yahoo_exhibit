const dbConfig = require("../config/db.config.js");
const Sequelize = require("sequelize");
const sequelize = new Sequelize(dbConfig.DB, dbConfig.USER, dbConfig.PASSWORD, {
	host: dbConfig.HOST,
	dialect: dbConfig.dialect,
	operatorsAliases: false,
	pool: {
		max: dbConfig.pool.max,
		min: dbConfig.pool.min,
		acquire: dbConfig.pool.acquire,
		idle: dbConfig.pool.idle
	}
});
const db = {};
db.Sequelize = Sequelize;
db.sequelize = sequelize;


// Other
db.userList = require("./User.model.js")(sequelize, Sequelize);

// Amazon
db.amazonSettingList = require("./AmazonSetting.model.js")(sequelize, Sequelize);
db.amazonItemList = require("./AmazonItem.model.js")(sequelize, Sequelize);
db.yahooStoreItemList = require("./YahooStoreItem.model.js")(sequelize, Sequelize);

// Yahoo
db.yahooSettingList = require("./YahooSetting.model.js")(sequelize, Sequelize);
db.yahooStoreList = require("./YahooStore.model.js")(sequelize, Sequelize);
db.yahooStoreItemList = require("./YahooStoreItem.model.js")(sequelize, Sequelize);
db.yahooOrderItemList = require("./YahooOrderItem.model.js")(sequelize, Sequelize);
db.exSettingList = require("./Exsetting.model.js")(sequelize, Sequelize);


module.exports = db;