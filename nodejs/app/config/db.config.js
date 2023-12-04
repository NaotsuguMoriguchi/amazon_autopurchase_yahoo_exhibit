module.exports = {
	HOST: "localhost",
	// USER: "root",
	// PASSWORD: "",
	// DB: "yahoo_order",
	USER: "xs767540_admin",
	PASSWORD: "kazu0828",
	DB: "xs767540_dbtest",
	dialect: "mysql",
	pool: {
		max: 5,
		min: 0,
		acquire: 30000,
		idle: 10000
	}
};