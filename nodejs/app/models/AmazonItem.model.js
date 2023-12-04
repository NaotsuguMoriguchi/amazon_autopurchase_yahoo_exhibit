module.exports = (sequelize, Sequelize) => {
	const AmazonItemList = sequelize.define("amazon_items", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_id: {
			type: Sequelize.INTEGER
		},
		category: {
			type: Sequelize.STRING
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
		asin: {
			type: Sequelize.STRING
		},
		jan: {
			type: Sequelize.STRING
		},
		am_price: {
			type: Sequelize.INTEGER
		},
		img_url: {
			type: Sequelize.STRING
		},
		shop_url: {
			type: Sequelize.STRING
		},
		exhibit: {
			type: Sequelize.INTEGER
		},
	},
	{ 
		timestamps: false
	});
	return AmazonItemList;
};