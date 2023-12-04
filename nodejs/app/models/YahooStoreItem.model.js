module.exports = (sequelize, Sequelize) => {
	const YahooStoreItemList = sequelize.define("yahoo_store_items", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_id: {
			type: Sequelize.INTEGER
		},
		amazon_category: {
			type: Sequelize.STRING
		},
		yahoo_category: {
			type: Sequelize.INTEGER
		},
		name: {
			type: Sequelize.STRING
		},
		caption: {
			type: Sequelize.STRING
		},
		dimension: {
			type: Sequelize.STRING
		},
		item_code: {
			type: Sequelize.STRING
		},
		asin: {
			type: Sequelize.STRING
		},
		jan: {
			type: Sequelize.STRING
		},
		amazon_price: {
			type: Sequelize.INTEGER
		},
		yahoo_price: {
			type: Sequelize.INTEGER
		},
		img_url: {
			type: Sequelize.STRING
		},
		shop_url: {
			type: Sequelize.STRING
		},
		stock: {
			type: Sequelize.INTEGER
		},
		is_updated: {
			type: Sequelize.INTEGER
		},
	},
	{ 
		timestamps: false
	});
	return YahooStoreItemList;
};