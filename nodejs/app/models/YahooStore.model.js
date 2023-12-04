module.exports = (sequelize, Sequelize) => {
	const YahooStoreList = sequelize.define("yahoo_stores", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_name: {
			type: Sequelize.STRING
		},
		order_count: {
			type: Sequelize.INTEGER
		},
	},
	{ 
		timestamps: false
	});
	return YahooStoreList;
};