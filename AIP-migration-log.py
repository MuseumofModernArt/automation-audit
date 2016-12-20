#!/usr/bin/env python2

# for monitoring AIP store migration

import json, urllib2, datetime, csv, os

API_root='http://archivematica.museum.moma.org:8000'
rest_of_url = '/api/v2/file/?username=binder&api_key=38e381ab1aa8035deb97d28af4395985aedebed6&limit=2&offset=0&format=json'
API_url = API_root+rest_of_url

request = json.load(urllib2.urlopen(API_url))
next = request['meta']['next']

arkivum_production = '/api/v2/location/0e31d09e-7ee9-40c3-bacf-2746ddebec7d/'
vnx_AIP_store = '/api/v2/location/27821f43-20e4-4b11-8e56-226b8d9d0179/'

package_count = 0
arkivum_count = 0
vnx_count = 0

i = datetime.datetime.now()
now = i.isoformat()
now = now[:-16]

print now

while next != None:	
	object_list = request['objects']
	for item in object_list:
		package_count = package_count+1
		print package_count, item['uuid'], item['current_location']
		if item['current_location'] == arkivum_production:
			arkivum_count = arkivum_count+1
		if item['current_location'] == vnx_AIP_store:
			vnx_count = vnx_count+1
	API_url = API_root+next
	request = json.load(urllib2.urlopen(API_url))
	next = request['meta']['next']
	
print 'number of AIPs in Arkivum: ', arkivum_count
print 'number of AIPs in VNX: ', vnx_count


with open ('AIP-migration.csv', 'rb') as csvfile:
	c = csv.writer(open('AIP-migration-2.csv', 'wb'))
	reader = csv.reader(csvfile, delimiter=',')
	for row in reader:
		if row[0] != now:
			c.writerow(row)
	c.writerow([now,arkivum_count,vnx_count])

os.rename('AIP-migration-2.csv','AIP-migration.csv')