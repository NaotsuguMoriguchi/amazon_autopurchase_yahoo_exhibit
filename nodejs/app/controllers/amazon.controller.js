const amazonPaapi = require("amazon-paapi");
const { userList, amazonItemList, amazonSettingList } = require("../models");


class GetItemInfo {
	constructor(amSetting, code) {
		this.amSetting = amSetting;
		this.code = code;
	}

	async main() {
		let commonParameters = {
			AccessKey: this.amSetting.access_key,
			SecretKey: this.amSetting.secret_key,
			PartnerTag: this.amSetting.partner_tag,
			PartnerType: "Associates",
			Marketplace: "www.amazon.co.jp",
		};

		let requestParameters = {
			ItemIds: this.code,
			ItemIdType: "ASIN",
			Condition: "New",
			Resources: [
				"Offers.Summaries.LowestPrice",
				"Offers.Listings.Condition.SubCondition",
				"Offers.Listings.Price",
				"Images.Primary.Large",
				"Images.Variants.Large",
				"ItemInfo.Title",
				"ItemInfo.ExternalIds",
				"ItemInfo.Classifications",
				"ItemInfo.ByLineInfo",
				"ItemInfo.Features",
				"ItemInfo.ProductInfo",
			],
		};

		// console.log(commonParameters, requestParameters)

		// Convert ASIN to JAN and store in db.
		await amazonPaapi
			.GetItems(commonParameters, requestParameters)
			.then((amazonData) => {
				// check if the response (amazonData) has errors and store in db
				if (amazonData.Errors !== undefined && amazonData.Errors.length > 0) {
					var errors = amazonData.Errors;
					for (const e of errors) {
						var query = {
							user_id: this.amSetting.user_id,
							name: "無効な ASIN コード",
							asin: e.Message.substr(11, 10),
						};

						// amazonItemList.create(query);
					}
				}

				// get JAN code from the response (amazonData) and store in db
				var items = amazonData.ItemsResult.Items;
				for (const i of items) {

					try {
						var query = {};

						query.user_id = this.amSetting.user_id;
						query.store_id = this.amSetting.store_id;
						query.asin = i.ASIN;

						if (i.ItemInfo === undefined || i.ItemInfo.ExternalIds == false) {
							console.log(`ASIN ${i.ASIN}に一致する商品は見つかりませんでした。\nASIN ${i.ASIN}に一致するJANコードは見つかりませんでした。`);
							
						} else {
							query.jan = i.ItemInfo.ExternalIds.EANs.DisplayValues[0];
							
							if (i.ItemInfo.Title) {
								query.name = i.ItemInfo.Title.DisplayValue;
							}

							if (i.ItemInfo.Classifications) {
								query.category = i.ItemInfo.Classifications.Binding.DisplayValue;
							}

							if (i.ItemInfo.Features) {
								query.caption = JSON.stringify(i.ItemInfo.Features.DisplayValues);
							}

							if (i.ItemInfo.ProductInfo) {
								query.dimension = JSON.stringify(i.ItemInfo.ProductInfo.ItemDimensions);
							}

							if (i.DetailPageURL !== undefined && i.DetailPageURL !== "") {
								query.shop_url = i.DetailPageURL;
							}
							
							if (i.Images.Primary !== undefined) {
								query.img_url = i.Images.Primary.Large.URL;
								if (i.Images.Variants !== undefined) {
									i.Images.Variants.forEach(img => {
										query.img_url += ',' + img.Large.URL;
									});
								}
							}

							if (i.Offers.Listings.length > 0 && i.Offers.Listings[0].Price.Amount != '0') {
								query.am_price = i.Offers.Listings[0].Price.Amount;
							}

							// console.log(query);
							amazonItemList.create(query);
						}


					} catch (err) {
						console.log("---------- forof item error ----------");
					}
				}
			})
			.catch((err) => {
				console.log("---------- amazon data CATCH error ----------");
				// for (const c of this.code) {
				// 	let query = {};
				// 	query.user_id = this.amSetting.user_id;
				// 	query.asin = c;
				// 	// amazonItemList.create(query);
				// }
			});
	}
}

const amazonInput = (amSetting, codeList) => {
	// console.log(amSetting, codeList);

	try {
		var index = 0;
		var len = codeList.length;

		var inputInterval = setInterval(() => {
			if (index < len) {
				let getItemInfo = new GetItemInfo(
					amSetting,
					codeList.slice(index, index + 10)
				);
				getItemInfo.main();
				index += 10;
			} else {
				clearInterval(inputInterval);
			}
		}, 3 * 1000);
	} catch (err) {
		console.log("Cannot input amazon information");
	}
};


exports.getInfo = (req, res) => {
	let reqData = JSON.parse(req.body.registerData);
	// console.log(reqData);
	var user_id = reqData.user_id;
	var store_id = reqData.store_id;

	amazonSettingList.findAll({ where: { store_id: store_id } })
		.then(amSettings => {
			amazonInput(amSettings[0], reqData.codes);

		}).catch(err => {
			console.log('---------- amazonSettingList error ----------', err.message);

		});
};
