module.exports = (sequelize, Sequelize) => {
	const ExsettingList = sequelize.define("exhibit_settings", {
		user_id: {
			type: Sequelize.INTEGER
		},
		amazon_setting: {
			type: Sequelize.STRING
		},
		yahoo_setting: {
			type: Sequelize.STRING
		},
		not_asin: {
			type: Sequelize.STRING
		},
		not_word: {
			type: Sequelize.STRING
		},
		remove_word: {
			type: Sequelize.STRING
		},
		invalid_word: {
			type: Sequelize.STRING
		},
		price_settings: {
			type: Sequelize.INTEGER
		},
		commission: {
			type: Sequelize.INTEGER
		},
		expenses: {
			type: Sequelize.INTEGER
		},
	},
	{ 
		timestamps: false
	});
	return ExsettingList;
};