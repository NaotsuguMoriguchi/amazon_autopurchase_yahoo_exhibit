const axios = require("axios");
const querystring = require("querystring");
const { yahooSettingList } = require("../models");


exports.newAuthorization = async (store_id, code) => {
    console.log(store_id, code);

    yahooSettingList.findOne({ where: { store_id: store_id } })
        .then(yahooSetting => {

            let yahoo_id = yahooSetting.yahoo_id;
            let yahoo_secret = yahooSetting.yahoo_secret;
            
            let data = yahoo_id + ":" + yahoo_secret;
            const basic_token = Buffer.from(data).toString("base64");
            
            // Get new Access Token
            let token_url= 'https://auth.login.yahoo.co.jp/yconnect/v2/token'
            let access_token_params = {
                grant_type: 'authorization_code',
                redirect_uri: 'https://xs767540.xsrv.jp/',
                code,
            }

            axios.post(token_url, querystring.stringify(access_token_params), {
                headers: {
                    'Authorization': `Basic ${basic_token}`,
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then((res) => {
                access_token = res.data.access_token;
                id_token = res.data.id_token;
                refresh_token = res.data.refresh_token;

                yahooSettingList.update(
                    {
                        access_token: access_token,
                        id_token: id_token,
                        refresh_token: refresh_token,
                        created_refresh_token: new Date(),
                    }, {where: {store_id: store_id}});
                
            }).catch((err) => {
                console.log(err, '=====>err');
            });

        }).catch(err => {
			console.log("+++++++++++-------- catch error --------+++++++++++", err.message);
		});
    
}

exports.reAuthorization = (store_id) => {

    yahooSettingList.findOne({ where: { store_id: store_id } })
        .then(yahooSetting => {

            let yahoo_id = yahooSetting.yahoo_id;
            let yahoo_secret = yahooSetting.yahoo_secret;
            let refresh_token = yahooSetting.refresh_token;

            let data = yahoo_id + ":" + yahoo_secret;
            const basic_token = Buffer.from(data).toString("base64");


            // Get Access Token from refresh token
            let token_url= 'https://auth.login.yahoo.co.jp/yconnect/v2/token'
            let access_token_params = {
                grant_type: 'refresh_token',
                refresh_token: refresh_token,
            }

            axios.post(token_url, querystring.stringify(access_token_params), {
                headers: {
                    'Authorization': `Basic ${basic_token}`,
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).then((res) => {
                // console.log(res);
                access_token = res.data.access_token;
                yahooSettingList.update({ access_token: access_token}, {where: {store_id: store_id}});

            }).catch((err) => {
                console.log(err, '=====>err');
            });

        }).catch(err => {
			console.log("+++++++++++-------- catch error --------+++++++++++", err.message);
		});

}
