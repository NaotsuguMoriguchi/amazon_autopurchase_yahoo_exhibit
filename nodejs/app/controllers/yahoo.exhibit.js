const axios = require('axios');
const querystring = require('querystring');
const makeDir = require('make-dir');
const download = require('image-downloader');
const FormData = require('form-data');
const fs = require('fs');
const fse = require('fs-extra');
const xml2js = require('xml2js');
const archiver = require('archiver');

const yahoo_token = require("../controllers/yahoo.token.js");
const { amazonItemList, yahooStoreItemList, yahooSettingList, yahooStoreList } = require("../models");


const img_download = async (item_imgs, storeImgDir, jancode) => {

	for ( let i = 0, len = item_imgs.length; i < Math.min( 5, len ); i++ ) {
		let file_name = (i == 0) ? (`${storeImgDir}/AYP-${jancode}.jpg`) : (`${storeImgDir}/AYP-${jancode}_${i}.jpg`);
		
		await download.image({
			url: item_imgs[i],
			dest: file_name,
		})
		.then((image) => {
			console.log(':) Wow, your image has downloaded successfully and you can find it on public folder! :)');
			
		})
		.catch((err) => {
			console.log(':( Whoops, your image download operation failed! :(');
	
		});
	}
	
}


const countJpgFiles = (folderPath) => {
	const files = fs.readdirSync(folderPath);
	const jpgFiles = files.filter((file) => file.endsWith('.jpg'));
	return jpgFiles.length;
}

const createZipFiles = async (folderPath, batchSize, maxCount) => {
	const files = fs.readdirSync(folderPath).filter((file) => file.endsWith('.jpg'));
	let count = 0;
	let zipIndex = 1;
	let fileIndex = 0;

	try {
	  while (count < maxCount && fileIndex < files.length) {
		const zipFilePath = `${folderPath}/img${zipIndex}.zip`;
		const output = fs.createWriteStream(zipFilePath);
		const archive = archiver('zip', { zlib: { level: 9 } });
  
		await new Promise((resolve, reject) => {
		  output.on('close', resolve);
		  archive.on('error', reject);
  
		  archive.pipe(output);
  
		  const batchEndIndex = Math.min(fileIndex + batchSize, files.length);
		  for (let i = fileIndex; i < batchEndIndex; i++) {
			const filePath = `${folderPath}/${files[i]}`;
			archive.file(filePath, { name: files[i] });
			fileIndex++;
			count++;
		  }
  
		  archive.finalize();
		});
  
		console.log(`Batch ${zipIndex} zipped successfully!`);
		zipIndex++;
	  }
  
	  return true;
	} catch (error) {
	  console.error('Error zipping files:', error);
	  throw error;
	}
}

const removeFilesAndFolders = (folderPath) => {
	if (fs.existsSync(folderPath)) {
	  fse.emptyDirSync(folderPath);
	  console.log(`All files and folders in ${folderPath} have been removed successfully!`);
	} else {
	  console.log(`Directory ${folderPath} does not exist.`);
	}
}


const img_upload = async (uploadImgPack_url, yahoo_auth_token, storeImgDir, zipFiles) => {

	const uploadPromises = zipFiles.map(async (zipFile) => {
		try {
			const zipFilePath = `${storeImgDir}/${zipFile}`;
			const formData = new FormData();
			formData.append('file', fs.createReadStream(zipFilePath));
	  
			const headers = {
				'Content-Type': `multipart/form-data; boundary=${formData._boundary}`,
				'Authorization': yahoo_auth_token
			};
	  
			const response = await axios.post(uploadImgPack_url, formData, { headers });
			console.log(`File ${zipFile} uploaded successfully`);
	  
		} catch (error) {
			console.error(`Error uploading file ${zipFile}:`, error);
		}
	});

	await Promise.all(uploadPromises);
}



const exhibit_Main = async (store_id, access_token) => {
	// console.log('Main1', store_id, access_token);
	
	const yahooStore = await yahooStoreList.findOne({
		where: { id: Number(store_id) },
		attributes: ['user_id', 'store_name']
	});
	
	const yahoo_auth_token = "Bearer " + access_token;
	const yahoo_editItem_url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/editItem";
	const yahoo_setStock_url = "https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/setStock";
	const uploadImgPack_url = `https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/uploadItemImagePack?seller_id=${yahooStore.store_name}`;
	const storeImgDir = await makeDir('../public/uploads/itemimages/' + store_id);
	
	
	amazonItemList.findAll({ where: { store_id: store_id, exhibit: 0 } })
		.then(exhibit_items => {

			var index = 0;
			var len = exhibit_items.length;

			let exInterval = setInterval(async () => {
				if (index < len) {

					var i = exhibit_items[index];
					var item_imgs = (i.img_url).split(',');

					if (item_imgs.length > 0) {
						await img_download(item_imgs, storeImgDir, i.jan);
					}
					// console.log('here is under Down.');

					var sale_price = Math.round(i.am_price * 1.1);
					var yahoo_category = 1111;

					var save_info = {
						'seller_id': yahooStore.store_name,
						'item_code': 'AYP' + '-' + i.jan,
						'path': i.category,
						'name': i.name,
						'product_category': yahoo_category,
						'price': sale_price,
						// 'original_price': i.am_price,
						// 'member_price': i.am_price - 1000,
						// 'subcode_images': '{"subcodetest-1":{"urls":["' + storeImgDir + '/' + 'AYP-' + i.jan + '.jpg' + '"], "main_flag":1},"subcodetest-2":{"urls":["' + storeImgDir + '/' + 'AYP-' + i.jan + '.jpg' + '_1.jpg' + '"], "main_flag":0}}',
						// 'item_image_urls': '"' + storeImgDir + '/' + 'AYP-' + i.jan + '.jpg' + ';"',
						'jan': i.jan,
						'caption': i.caption,
						'delivery': 1,
					};

					axios.post(yahoo_editItem_url, querystring.stringify(save_info), { headers: { 'Authorization': yahoo_auth_token } })
					.then((res) => {
						console.log(':) Wow, yahoo exhibition succeeded! :)');

						var stockInfo = {
							'seller_id': yahooStore.store_name,
							'item_code': 'AYP-' + i.jan,
							'quantity': 3,
							'allow-overdraft': 1,
							'stock-close': 0
						};

						axios.post(yahoo_setStock_url, querystring.stringify(stockInfo), { headers: { 'Authorization': yahoo_auth_token } })
						.then((res) => {
							console.log(':) Wow, stockinfo updated successfully! :)');

							var query = {};
							query.user_id = yahooStore.user_id;
							query.store_id = store_id;
							query.amazon_category = i.category;
							query.yahoo_category = yahoo_category;
							query.name = i.name;
							query.caption = i.caption;
							query.dimension = i.dimension;
							query.item_code = 'AYP' + '-' + i.jan;
							query.asin = i.asin;
							query.jan = i.jan;
							query.amazon_price = i.am_price;
							query.yahoo_price = sale_price;
							query.img_url = i.img_url;
							query.shop_url = i.shop_url;
							query.stock = 3;
							query.is_updated = 0;
	
							yahooStoreItemList.create(query);

							amazonItemList.update(
								{
									exhibit: 1
								},
								{
									where: {
										user_id: yahooStore.user_id,
										id: i.id,
										asin: i.asin,
									}
								}
							);

						}).catch((err) => {
							console.log(':( Whoops! stockinfo update failed! :(');
						});

						
					}).catch((err) => {
						console.log(':( Whoops!, yahoo exhibition failed! :(', err);
					});

					index++;

				} else {
					clearInterval(exInterval);
					console.log('exhibit end.');

					const folderPath = storeImgDir;
					const batchSize = 200;
					const maxCount = countJpgFiles(storeImgDir);

					try {
						await createZipFiles(folderPath, batchSize, maxCount);
						console.log('All batches zipped successfully!');
						
						const zipFiles = fs.readdirSync(folderPath).filter((file) => file.endsWith('.zip'));
						console.log('Zip files length:', zipFiles.length, zipFiles[0], '\n', 'Zip files:', zipFiles);
						
						await img_upload(uploadImgPack_url, yahoo_auth_token, storeImgDir, zipFiles);
						console.log('All Zip files uploaded successfully!');

						removeFilesAndFolders(folderPath);
					} catch (error) {
						console.error('Error zipping files:', error);
					}
					console.log('make all zip.');

					// logList.findAll({
					// 	where: {
					// 		user_id: user_id,
					// 		csv: fileName,
					// 	}
					// }).then((res) => {
					// 	if (res.length) {
					// 		logList.update(
					// 			{
					// 				user_id: user_id,
					// 				exhibited: len,
					// 				uploaded: len,
					// 				csv: fileName,
					// 				status: 2
					// 			},
					// 			{
					// 				where: {
					// 					user_id: user_id,
					// 					csv: fileName,
					// 				}
					// 			}
					// 		);
					// 	} else {
					// 		logList.create({
					// 			user_id: user_id,
					// 			exhibited: len,
					// 			uploaded: len,
					// 			csv: fileName,
					// 			status: 2
					// 		});
					// 	}
					// });

					// axios.get(`https://xs877048.xsrv.jp/fmproxy/api/v1/yahoo/stock_csv?user_id=${user_id}`)
					// 	.then((res) => {
					// 		console.log(res);
					// 	})
					// 	.catch((err) => {

					// 	});
					// stock data csv file
					// const writer = csvWriter({ headers: ['code', 'sub-code', 'quantity', 'mode', 'allow-overdraft', 'stock-close'] });
					// writer.pipe(fs.createWriteStream('stock_data.csv'));
					// csvFileArr.forEach(item => {
					// 	writer.write(item);
					// });

					// writer.end();
					// const file = userCsvDir + '/stock_data.csv';
					// await res.download(file);

					// let data = new FormData();
					// data.append('file', fs.createReadStream(userCsvDir + '/stock_data.csv'));

					// let config = {
					// 	method: 'post',
					// 	maxBodyLength: Infinity,
					// 	url: 'https://circus.shopping.yahooapis.jp/ShoppingWebService/V1/uploadStockFile?seller_id=haruharu202210278',
					// 	headers: {
					// 		'Authorization': yahoo_auth_token,
					// 		'Content-Type': 'multipart/form-data',
					// 		...data.getHeaders()
					// 	},
					// 	data: data
					// };

					// axios.request(config)
					// 	.then((response) => {
					// 		console.log(':) Wow, csv uploaded successfully! :)');
					// 		// console.log(JSON.stringify(response.data));
					// 	})
					// 	.catch((err) => {
					// 		console.log(':( Whoops, csv upload failed! :(');
					// 		// console.log(error);
					// 	});
				
				}
			}, 2 * 1000);
		}).catch(err => {
			console.log('+++++++++++-------- itemlist error --------+++++++++++', err.message);
		});

}


const exhibit_get_token = (store_id) => {

	yahooSettingList.findOne({ where: { store_id: store_id } })
        .then(yahooSetting => {
			let access_token = yahooSetting.access_token;
			exhibit_Main(store_id, access_token);

        }).catch(err => {
			console.log("+++++++++++-------- catch error --------+++++++++++", err.message);
		});
}


exports.exhibit = async (req, res) => {

	let user_id = Number(req.body.user_id);
	let store_id = Number(req.body.store_id);
	let code = req.body.code;
	
	if (req.body.authorization == 'new') {

		await yahoo_token.newAuthorization(store_id, code);

		setTimeout(() => {
			exhibit_get_token(store_id);
			res.status(200).send("Yahoo exhibit successfully");
			
		}, 3000);

	} else {

		await yahoo_token.reAuthorization(store_id);

		setTimeout(() => {
			exhibit_get_token(store_id);
			res.status(200).send("Yahoo exhibit successfully");
			
		}, 3000);

	}

}
