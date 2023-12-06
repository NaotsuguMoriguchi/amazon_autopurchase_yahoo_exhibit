const axios = require('axios');
const querystring = require('querystring');
const makeDir = require('make-dir');
const download = require('image-downloader');
const FormData = require('form-data');
const fs = require('fs');
const xml2js = require('xml2js');

const yahoo_token = require("../controllers/yahoo.token.js");
const { userList, yahooStoreList, yahooSettingList, yahooOrderItemList } = require("../models");


const orderInfoMain = (orderInfo, yahooStore, access_token) => {
	// console.log(orderInfo);

	const url = 'https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/orderInfo';
	const param = `
	<Req>
		<Target>
			<OrderId>${orderInfo.OrderId}</OrderId>
			<Field>OrderTime,ShipInvoiceNumber2,OrderId,ItemId,Title,Quantity,ShipFirstName,ShipLastName,UnitPrice,TotalPrice,ShipFirstNameKana,ShipLastNameKana,ShipZipCode,ShipPrefecture,ShipCity,ShipAddress1,ShipAddress2,ShipPhoneNumber</Field>
		</Target>
		<SellerId>${yahooStore.store_name}</SellerId>
	</Req>`;

	let config = {
		method: 'post',
		maxBodyLength: Infinity,
		url: url,
		headers: {
			'Authorization': `Bearer ${access_token}`
		},
		data: param,
	};

	axios.request(config)
		.then((response) => {
			// console.log(JSON.stringify(response.data));
			
			xml2js.parseString(response.data, (err, result) => {
				if (err) {
					console.error(err);
					return;
				}

				let OrderTime = new Date(result.ResultSet.Result[0].OrderInfo[0].OrderTime[0]);
					OrderTime.setHours(OrderTime.getHours() + 9);
				let ShipInvoiceNumber2 = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipInvoiceNumber2[0];
				let OrderId = result.ResultSet.Result[0].OrderInfo[0].OrderId[0];
				let ItemId = result.ResultSet.Result[0].OrderInfo[0].Item[0].ItemId[0];
				let Title = result.ResultSet.Result[0].OrderInfo[0].Item[0].Title[0];
				let Quantity = result.ResultSet.Result[0].OrderInfo[0].Item[0].Quantity[0];
				let ShipFirstName = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipFirstName[0];
				let ShipLastName = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipLastName[0];
				let UnitPrice = result.ResultSet.Result[0].OrderInfo[0].Item[0].UnitPrice[0];
				let ShipFirstNameKana = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipFirstNameKana[0];
				let ShipLastNameKana = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipLastNameKana[0];
				let ShipZipCode = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipZipCode[0];
				let ShipPrefecture = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipPrefecture[0];
				let ShipCity = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipCity[0];
				let ShipAddress1 = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipAddress1[0];
				let ShipAddress2 = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipAddress2[0];
				let ShipPhoneNumber = result.ResultSet.Result[0].OrderInfo[0].Ship[0].ShipPhoneNumber[0];
				console.log(`OrderTime: ${OrderTime}, ShipInvoiceNumber2: ${ShipInvoiceNumber2}, OrderId: ${OrderId}, ItemId: ${ItemId}, Title: ${Title}, Quantity: ${Quantity}, ShipFirstName: ${ShipFirstName}, ShipLastName: ${ShipLastName}, UnitPrice: ${UnitPrice}, ShipFirstNameKana: ${ShipFirstNameKana}, ShipLastNameKana: ${ShipLastNameKana}, ShipZipCode: ${ShipZipCode}, ShipPrefecture: ${ShipPrefecture}, ShipCity: ${ShipCity}, ShipAddress1: ${ShipAddress1}, ShipAddress2: ${ShipAddress2}, ShipPhoneNumber: ${ShipPhoneNumber}`)

				var createQuery = {
					user_id: yahooStore.user_id,
					store_id: yahooStore.id,
					order_id: OrderId,
					item_id: ItemId,
					order_time: OrderTime,
					ship_invoicenumber2: ShipInvoiceNumber2,
					title: Title,
					quantity: Quantity,
					ship_firstname: ShipFirstName,
					ship_lastname: ShipLastName,
					unit_price: UnitPrice,
					line_subtotal: UnitPrice * Quantity,
					total_price: UnitPrice * Quantity,
					ship_firstname_kana: ShipFirstNameKana,
					ship_lastname_kana: ShipLastNameKana,
					ship_zipcode: ShipZipCode,
					ship_prefecture: ShipPrefecture,
					ship_city: ShipCity,
					ship_address1: ShipAddress1,
					ship_address2: ShipAddress2,
					ship_phonenumber: ShipPhoneNumber,
				};

				yahooOrderItemList.create(createQuery);

			});
		})
		.catch((error) => {
			console.log(error);
		});
}

const orderListMain = (yahooStore, access_token) => {
	
	const today = new Date();
	const year = today.getFullYear();
	const month = String(today.getMonth() + 1).padStart(2, '0');
	const day = String(today.getDate()).padStart(2, '0');
	const hours = String(today.getHours()).padStart(2, '0');
	const minutes = String(today.getMinutes()).padStart(2, '0');
	const seconds = String(today.getSeconds()).padStart(2, '0');
	const formattedTodayDateTime = `${year}${month}${day}${hours}${minutes}${seconds}`;
	console.log(formattedTodayDateTime);

	
	//	,OrderTime,ShipInvoiceNumber2,ShipFirstName,ShipLastName,TotalPrice,ShipFirstNameKana,ShipLastNameKana,ShipPrefecture,ShipAddress1,ShipAddress2,ShipPhoneNumber
	const url = 'https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/orderList';
	const param = `
	<Req>
		<Search>
			<Condition>
				<OrderTimeFrom>20230101000000</OrderTimeFrom>
				<OrderTimeTo>${formattedTodayDateTime}</OrderTimeTo>
				<OrderStatus>2</OrderStatus>
				<IsSeen>false</IsSeen>
			</Condition>
			<Result>2000</Result>
			<Field>OrderId</Field>
		</Search>
		<SellerId>${yahooStore.store_name}</SellerId>
	</Req>`;
	
	let config = {
		method: 'post',
		maxBodyLength: Infinity,
		url: url,
		headers: {
			'Authorization': `Bearer ${access_token}`
		},
		data: param,
	};
	axios.request(config)
		.then((response) => {
			// console.log(JSON.stringify(response.data));

			var deleteQuery = {
				where: {
					store_id: yahooStore.id
				}
			}
			yahooOrderItemList.destroy(deleteQuery);

			xml2js.parseString(response.data, (err, result) => {
				if (err) {
					console.error(err);
					return;
				}

				const orderTotalCount = result.Result.Search[0].TotalCount[0];
				// console.log('Order TotalCount:', orderTotalCount);
				
				const orderInfos = result.Result.Search[0].OrderInfo;

				try {
					var index = 0;
					var len = orderInfos.length;

					let orderInfoInterval = setInterval(() => {

						if (index < len) {
							// console.log('index-------', index);
							orderInfoMain(orderInfos[index], yahooStore, access_token);
							index += 1;

						} else {
							clearInterval(orderInfoInterval);
							return;

						}
					}, 1.1 * 1000);

				} catch (error) {
					console.log('--  orderInfos: undefined  --', error.message);
				}

			});

		})
		.catch((error) => {
			console.log(error);
		});

}

const orderCountMain = (yahooStore, access_token) => {
	// console.log('ordercountMain------', yahooStore);

	let config = {
		method: 'get',
		maxBodyLength: Infinity,
		url: `https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/orderCount?sellerId=${yahooStore.store_name}`,
		headers: {
			'Authorization': `Bearer ${access_token}`
		}
	};

	axios.request(config)
	.then((response) => {
		// console.log(JSON.stringify(response.data));

		var newOrder = 0;
		// Parse the XML string into an object
		xml2js.parseString(response.data, (err, result) => {
			if (err) {
				console.error(err);
				return;
			}

			const newOrderText = result.ResultSet.Result[0].Count[0].NewOrder[0];
			// console.log(newOrderText);
			newOrder = newOrderText;
		});

		yahooStoreList.update({ order_count: newOrder}, {where: {id: yahooStore.id}});
		return;

	})
	.catch((error) => {
		console.log(error);
	});
}

const yahoo_orderMain = async (store_id, access_token) => {
	// console.log('asdf', store_id, access_token);

	try {
		const yahooStores = await yahooStoreList.findAll({ where: { id: store_id } });
		// console.log(yahooStores);

		var index = 0;
		var len = yahooStores.length;

		let orderCountInterval = setInterval(() => {

			if (index < len) {
				// console.log('index-------', index);
				orderCountMain(yahooStores[index], access_token);
				orderListMain(yahooStores[index], access_token);
				index += 1;

			} else {
				clearInterval(orderCountInterval);
				return;

			}
		}, 1.5 * 1000);

	} catch (error) {
		console.log("+++++++++++-------- catch error --------+++++++++++", error.message);
	}
}

const order_get_token = (store_id) => {

	yahooSettingList.findOne({ where: { store_id: store_id } })
        .then(yahooSetting => {
			let access_token = yahooSetting.access_token;
			// console.log(access_token);

			yahoo_orderMain(store_id, access_token);

        }).catch(err => {
			console.log("+++++++++++-------- catch error --------+++++++++++", err.message);
		});
	
}


exports.yahoo_orderCount = async (req, res) => {
	console.log(req.body);
	
	let user_id = Number(req.body.user_id);
	let store_id = Number(req.body.store_id);
	let code = req.body.code;

	if (req.body.authorization == 'new') {

		await yahoo_token.newAuthorization(store_id, code);

		setTimeout(() => {
			order_get_token(store_id);
			res.status(200).send("Yahoo OrderCount obtained successfully");
			
		}, 3000);

	} else {

		await yahoo_token.reAuthorization(store_id);

		setTimeout(() => {
			order_get_token(store_id);
			res.status(200).send("Yahoo OrderCount obtained successfully");
			
		}, 3000);
		
	}

}
