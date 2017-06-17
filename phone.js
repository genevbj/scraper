const CDP = require('chrome-remote-interface');
var sleep = require('sleep');

var purl = process.argv[2];

//console.log("processing " + purl);


CDP(async (client) => {
    try {
        const {DOM, Page, Runtime} = client;
        await DOM.enable();
        await Page.enable();
        await Page.navigate({url: purl});
        await Page.loadEventFired();

//      console.log("do click");

	await Runtime.evaluate({
	    expression: `document.querySelector("#contact_methods strong").click()`
	});

        
//      console.log("wait phone");
        sleep.sleep(3);
//      console.log("get phone");

	const ph = await Runtime.evaluate({
	    expression: `document.querySelector("#contact_methods strong").textContent`
	});



	console.log('{"phone": "' + ph.result.value+ '"}');



	client.close();


    } catch (err) {
        console.error(err);
    } finally {
        client.close();
    }
}).on('error', (err) => {
    console.error(err);
});

