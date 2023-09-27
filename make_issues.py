from __future__ import print_function
import time
import giteapy
from giteapy.rest import ApiException
from pprint import pprint
from datetime import date


configuration = giteapy.Configuration()
configuration.host = 'https://git.enhost.io/api/v1'
configuration.api_key['access_token'] = ''

api_instance = giteapy.IssueApi(giteapy.ApiClient(configuration))
owner = 'hcaz'
repo = 'advent-of-code'

year=2023
milestone=3

for x in range(1, 26):
    try:
        body = giteapy.CreateIssueOption(title='Day ' + str(x) + ' - ' + str(year), body='https://adventofcode.com/' + str(year) + '/day/' + str(x), assignee='hcaz', milestone=milestone, due_date=str(year) + '-12-' + str(x).zfill(2) + 'T00:00:00Z')
        api_response = api_instance.issue_create_issue(owner, repo, body=body)
        pprint(api_response)
    except ApiException as e:
        print("Exception when calling IssueApi->issue_create_issue: %s\n" % e)
