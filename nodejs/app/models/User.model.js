module.exports = (sequelize, Sequelize) => {
	const UserList = sequelize.define("users", {
		name: {
			type: Sequelize.STRING,
		},
		email: {
			type: Sequelize.STRING,
		},
		email_verified_at: {
			type: Sequelize.DATE,
		},
		password: {
			type: Sequelize.STRING,
		},
		remember_token: {
			type: Sequelize.STRING,
		},
		role: {
			type: Sequelize.ENUM('admin', 'user'),
			defaultValue: 'user'
		},
		registered_item: {
			type: Sequelize.INTEGER,
		},
		progress: {
			type: Sequelize.INTEGER,
		},
		limit_item: {
			type: Sequelize.INTEGER,
		},
	},
	{
		timestamps: false,
	});
	return UserList;
};
