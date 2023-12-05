const axios = require('axios');

const { yahooStoreItemList, yahooSettingList, yahooStoreList } = require("../models");




const update_Price = async (store_name, access_token, items) => {
	// console.log(store_name);

	const yahoo_auth_token = "Bearer " + access_token;
	const yahoo_updateItem_url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/updateItems";
	

	var priceString = `seller_id=${store_name}`;
	
	for (const [index, item] of items.entries()) {
		priceString += `&item${(index + 1)}=`;
		priceString += encodeURIComponent(`item_code=${item.item_code}&price=${item.yahoo_price}&sale_price=`);

	}

	const headers = { 'Authorization': yahoo_auth_token };

	await axios.post(yahoo_updateItem_url, priceString, { headers })
		.then( async (res) => {
			// console.log(res);
		
			for (const [index, item] of items.entries()) {
				await yahooStoreItemList.update( { is_updated: 0 }, {where: {id: item.id}} );
			}

		}).catch((err) => {
			console.log(':( Whoops!, yahoo price update failed! :(', err);

		});

}


const yahooTracking = async (store_id, store_name, access_token) => {
	// console.log(store_id, access_token);

	await yahooStoreItemList.findAll({ where: { store_id: store_id, is_updated: 1 } })
		.then( async (update_items) => {
			// console.log(update_items);
			var len = update_items.length;
			var index = 0;

			var inputInterval = setInterval(() => {
				if (index < len) {
					update_Price(store_name, access_token, (update_items.slice(index, index + 100)));
					index += 100;

				} else {
					clearInterval(inputInterval);

				}
			}, 3 * 1000);

			
		}).catch(err => {
			console.log('+++++++++++-------- itemlist error --------+++++++++++', err.message);
		});

}


const update_get_token = (store_id, store_name) => {

	yahooSettingList.findOne({ where: { store_id: store_id } })
        .then(yahooSetting => {
			let access_token = yahooSetting.access_token;
			// console.log(store_id, access_token);
			yahooTracking(store_id, store_name, access_token);

        }).catch(err => {
			console.log("+++++++++++-------- Yahoo API access token not found --------+++++++++++", err.message);
		});
}



exports.updateInfo = async () => {

    await yahooStoreList
		.findAll()
		.then((res) => {
			for (let yahooStore of res) {
				// console.log(yahooStore.id);
				update_get_token(yahooStore.id, yahooStore.store_name);
			}

		})
		.catch((err) => {
			console.log(':( Whoops! There is no user information! :(', err);
		});
};
