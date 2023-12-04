module.exports = (sequelize, Sequelize) => {
	const AmazonSettingList = sequelize.define("amazon_settings", {
		user_id: {
			type: Sequelize.INTEGER
		},
		store_id: {
			type: Sequelize.INTEGER
		},
		access_key: {
			type: Sequelize.STRING
		},
		secret_key: {
			type: Sequelize.STRING
		},
		partner_tag: {
			type: Sequelize.STRING
		},
		life_check: {
			type: Sequelize.INTEGER
		},
	},
	{ 
		timestamps: false
	});
	return AmazonSettingList;
};