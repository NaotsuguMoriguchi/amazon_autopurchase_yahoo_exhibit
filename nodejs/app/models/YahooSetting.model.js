module.exports = (sequelize, Sequelize) => {
	const YahooSettingList = sequelize.define("yahoo_settings", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_id: {
			type: Sequelize.INTEGER
		},
		yahoo_id: {
			type: Sequelize.STRING
		},
		yahoo_secret: {
			type: Sequelize.STRING
		},
		access_token: {
			type: Sequelize.STRING
		},
		id_token: {
			type: Sequelize.STRING
		},
		refresh_token: {
			type: Sequelize.STRING
		},
		created_refresh_token: {
			type: Sequelize.DATE
		},
	},
	{ 
		timestamps: false
	});
	return YahooSettingList;
};