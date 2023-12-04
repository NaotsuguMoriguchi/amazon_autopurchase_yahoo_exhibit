const amazonPaapi = require('amazon-paapi');
const { yahooStoreList, amazonSettingList, yahooStoreItemList } = require("../models");


class CheckAamzonInfo {
	constructor(store, apiSet, code) {
		this.store = store;
		this.apiSet = apiSet;
		this.code = code;
	}

	async main() {
		let commonParameters = {
			AccessKey: this.apiSet.access_key,
			SecretKey: this.apiSet.secret_key,
			PartnerTag: this.apiSet.partner_tag,
			PartnerType: 'Associates',
			Marketplace: 'www.amazon.co.jp',
		};

		let requestParameters = {
			ItemIds: this.code,
			ItemIdType: "ASIN",
			Condition: "New",
			Resources: [
				"Offers.Listings.Availability.Message",
				"Offers.Listings.Price",
				"ItemInfo.ExternalIds",
			],
		};
		// console.log('commonParameters: ', commonParameters, '\n', 'requestParameters: ', requestParameters);

		await amazonPaapi.GetItems(commonParameters, requestParameters)
			.then( async (amazonData) => {

				if (amazonData.Errors !== undefined && amazonData.Errors.length > 0) {
					var errors = amazonData.Errors;
					for (const e of errors) {
						var query = {
							is_updated: 9
						};

						yahooStoreItemList.update(query, {
							where: {
								store_id: this.store.id,
								asin: e.Message.substr(11, 10)
							}
						});
					}
				}

				var items = amazonData.ItemsResult.Items;
				for (const i of items) {
					try {
						var query = {};
						query.store_id = this.store.id;
						query.asin = i.ASIN;

						await yahooStoreItemList.findOne({ where: { store_id: query.store_id, asin: query.asin } })
							.then( async (storeItem) => {
								if (i.Offers.Listings.length > 0) {

									if (i.Offers.Listings[0].Price.Amount != storeItem.am_price) {
										query.am_price = i.Offers.Listings[0].Price.Amount;
										query.yahoo_price = Math.round(query.am_price * 1.1);
										query.is_updated = 1;

										await storeItem.update({
												am_price: query.am_price,
												yahoo_price: query.yahoo_price,
												is_updated: query.is_updated
											  });
									}
									if (storeItem.stock != 0 && i.Offers.Listings[0].Availability.Message != '在庫あり。') {
										query.is_updated = 2;
										
										await storeItem.update({
												stock: 0,
												is_updated: query.is_updated
											  });
									}
									if (storeItem.stock == 0 && i.Offers.Listings[0].Availability.Message == '在庫あり。') {
										query.is_updated = 2;
										
										await storeItem.update({
												stock: 3,
												is_updated: query.is_updated
											  });
									}

								}

							}).catch(err => {
								console.log('---------- item not found error ----------', err.message);
							});

					} catch (err) {
						console.log("---------- forof item error ----------");
					}
				}

				await amazonSettingList.findOne({ where: { access_key: this.apiSet.access_key, partner_tag: this.apiSet.partner_tag } })
					.then( async (amazonSetting) => {
						if (amazonSetting.life_check == 0) {
							await amazonSetting.update({
								life_check: 1
							});

						}
						
					}).catch(err => {
						console.log('---------- amazon paapi not found error ----------', err.message);
					});

			}).catch(err => {
				console.log('---------- amazon data tracking error (429) ----------', err.message);

				amazonSettingList.findOne({ where: { access_key: this.apiSet.access_key, partner_tag: this.apiSet.partner_tag } })
					.then( async (amazonSetting) => {
						await amazonSetting.update({
							life_check: 0
						});
					}).catch(err => {
						console.log('---------- amazon paapi not found error ----------', err.message);
					});
			});
	}
}


var amazonTracking = async (yahooStore) => {
	var paapiList = [];
	await amazonSettingList.findAll({ where: { store_id: yahooStore.id } })
		.then( amSettings => {

			var paapis = [];
			for (const set of amSettings) {
				let paapiSet = {
					access_key: set.access_key,
					secret_key: set.secret_key,
					partner_tag: set.partner_tag
				}
				paapis.push(paapiSet);
				
			}
			paapis.shift();
			paapiList = paapis;
			// console.log(paapiList);

		}).catch(err => {
			console.log('---------- itemlist error ----------', err.message);
		});
	// console.log(paapiList);


	await yahooStoreItemList.findAll({ where: { store_id: yahooStore.id } })
		.then(items => {
			var asinIndex = 0;
			var asinLen = items.length;
			var asins = [];
			var paapiIndex = 0;
			var apiLen = paapiList.length;

			for (const i of items) {
				asins.push(i.asin);
			}

			let checkInterval = setInterval(() => {
				if (asinIndex < asinLen) {
					// console.log('-----', asins.slice(asinIndex, (asinIndex + 10)));
					let checkAmazonInfo = new CheckAamzonInfo(yahooStore, paapiList[paapiIndex], asins.slice(asinIndex, (asinIndex + 10)));
					checkAmazonInfo.main();
					paapiIndex += 1;
					paapiIndex = (paapiIndex == apiLen) ? 0 : paapiIndex;
					asinIndex += 10;
				} else {
					clearInterval(checkInterval);
				}
			}, 10 * 1000);
		}).catch(err => {
			console.log('---------- itemlist error ----------', err.message);
		});
};


exports.updateInfo = async () => {
	await yahooStoreList
		.findAll()
		.then((res) => {
			for (let yahooStore of res) {
				amazonTracking(yahooStore);
				console.log(yahooStore.id);
			}
		})
		.catch((err) => {
			console.log(':( Whoops! There is no user information! :(');
		});
};
