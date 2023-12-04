module.exports = (sequelize, Sequelize) => {
	const YahooOrderItemList = sequelize.define("yahoo_order_items", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_id: {
			type: Sequelize.INTEGER
		},
		order_id: {
			type: Sequelize.STRING
		},
		item_id: {
			type: Sequelize.STRING
		},
		order_time: {
			type: Sequelize.DATE
		},
		ship_invoicenumber2: {
			type: Sequelize.STRING
		},
		title: {
			type: Sequelize.STRING
		},
		quantity: {
			type: Sequelize.INTEGER
		},
		ship_firstname: {
			type: Sequelize.STRING
		},
		ship_lastname: {
			type: Sequelize.STRING
		},
		unit_price: {
			type: Sequelize.INTEGER
		},
		line_subtotal: {
			type: Sequelize.INTEGER
		},
		total_price: {
			type: Sequelize.INTEGER
		},
		ship_firstname_kana: {
			type: Sequelize.STRING
		},
		ship_lastname_kana: {
			type: Sequelize.STRING
		},
		ship_zipcode: {
			type: Sequelize.STRING
		},
		ship_prefecture: {
			type: Sequelize.STRING
		},
		ship_city: {
			type: Sequelize.STRING
		},
		ship_address1: {
			type: Sequelize.STRING
		},
		ship_address2: {
			type: Sequelize.STRING
		},
		ship_phonenumber: {
			type: Sequelize.STRING
		},
	},
	{ 
		timestamps: false
	});
	return YahooOrderItemList;
};