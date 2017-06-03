
wget -O - --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" \
 --header="Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8" \
 --header="Accept-Language:ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4" \
 --header="Cache-Control:no-cache" \
 --header="Connection:keep-alive" \
 --header="Host:olx.uz" \
 --header="Pragma:no-cache" \
 --header="Upgrade-Insecure-Requests:1" \
 --save-headers=on \
 --referer="https://google.com" \
"http://olx.uz/obyavlenie/sdam-v-arendu-evro-dom-po-tsentr-lunocharskogo-IDkUKt.html" > U.html

# --load-cookies=COOKIE.txt \
# --save-cookies=COOKIE.txt \

ptoken=`grep phoneToken U.html |sed -e "s/^.*phoneToken = '\(.*\)'.*$/\1/gi"`
kook=`grep "Set-Cookie:\ pt" U.html |sed -e "s/^.*pt=\(.[^;]*\);.*$/\1/gi"`

echo $ptoken
echo $kook

#Cookie:newrelicInited=0; PHPSESSID=b8ntq3qa3n43fkl8g0fpokhdu3; mobile_default=desktop; layerappsSeen=1; last_locations=4-0-0-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C-tashkent; my_city_2=4_0_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C_tashkent; smart_top=1; from_detail=1; pt=d062137a926ec814288153ccebbbf389b50cfff988678b135710747501d4a84b90c10de23f9c4e34571184876449fd67562d5b56ab69fb279459e2647a415836; mp_ccb9dc9ef29ec6fed030230b5f8d16e9_mixpanel=%7B%22distinct_id%22%3A%20%2215c3fc7f0d90-038ab020d22c05-3060750a-1fa400-15c3fc7f0e3340%22%2C%22%24search_engine%22%3A%20%22google%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fwww.google.ru%2F%22%2C%22%24initial_referring_domain%22%3A%20%22www.google.ru%22%7D; onap=15c3fc7eccdx1b5ee172-12-15c63cee1c0x7917545a-3-1496325529
#Cookie:newrelicInited=0; mobile_default=desktop; layerappsSeen=1; last_locations=4-0-0-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C-tashkent; my_city_2=4_0_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C_tashkent; smart_top=1; from_detail=1; pt=d062137a926ec814288153ccebbbf389b50cfff988678b135710747501d4a84b90c10de23f9c4e34571184876449fd67562d5b56ab69fb279459e2647a415836; mp_ccb9dc9ef29ec6fed030230b5f8d16e9_mixpanel=%7B%22distinct_id%22%3A%20%2215c3fc7f0d90-038ab020d22c05-3060750a-1fa400-15c3fc7f0e3340%22%2C%22%24search_engine%22%3A%20%22google%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fwww.google.ru%2F%22%2C%22%24initial_referring_domain%22%3A%20%22www.google.ru%22%7D; onap=15c3fc7eccdx1b5ee172-12-15c63cee1c0x7917545a-3-1496325529


wget -O - --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36" \
 --header="Host:olx.uz" \
 --header="Connection:keep-alive" \
 --header="Pragma:no-cache" \
 --header="Cache-Control:no-cache" \
 --header="Accept:*/*" \
 --header="Accept-Language:ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4" \
 --header="X-Requested-With: XMLHttpRequest" \
 --header="Cookie: layerappsSeen=1; pt=$kook; last_locations=4-0-0-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C-tashkent; my_city_2=4_0_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C_tashkent; smart_top=1; from_detail=1; mp_ccb9dc9ef29ec6fed030230b5f8d16e9_mixpanel=%7B%22distinct_id%22%3A%20%2215c3fc7f0d90-038ab020d22c05-3060750a-1fa400-15c3fc7f0e3340%22%2C%22%24search_engine%22%3A%20%22google%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fwww.google.ru%2F%22%2C%22%24initial_referring_domain%22%3A%20%22www.google.ru%22%7D; onap=15c3fc7eccdx1b5ee172-12-15c63cee1c0x7917545a-5-1496325538" \
 --save-headers=on \
 --referer="http://olx.uz/obyavlenie/sdam-v-arendu-evro-dom-po-tsentr-lunocharskogo-IDkUKt.html" \
"http://olx.uz/ajax/misc/contact/phone/kUKt/?pt=$ptoken" > P.txt


#Cookie: layerappsSeen=1; last_locations=4-0-0-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82-%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C-tashkent; my_city_2=4_0_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82_0_%D0%A2%D0%B0%D1%88%D0%BA%D0%B5%D0%BD%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%BE%D0%B1%D0%BB%D0%B0%D1%81%D1%82%D1%8C_tashkent; smart_top=1; from_detail=1; mp_ccb9dc9ef29ec6fed030230b5f8d16e9_mixpanel=%7B%22distinct_id%22%3A%20%2215c3fc7f0d90-038ab020d22c05-3060750a-1fa400-15c3fc7f0e3340%22%2C%22%24search_engine%22%3A%20%22google%22%2C%22%24initial_referrer%22%3A%20%22https%3A%2F%2Fwww.google.ru%2F%22%2C%22%24initial_referring_domain%22%3A%20%22www.google.ru%22%7D; onap=15c3fc7eccdx1b5ee172-12-15c63cee1c0x7917545a-5-1496325538" \
