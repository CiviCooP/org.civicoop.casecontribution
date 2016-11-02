# org.civicoop.casecontribution

This extension links a contribution to a case. It also shows all linked 
contributions on the manage case screen. And you can also create a contribution
from within the manage case screen.

## Table of content
- [Audience of this document](audience-of-this-document)
- [What does this extension do?](what-does-this-extension-do)
- [When is this extension useful?](when-is-this-extension-useful)
- [When is this extension useful?](when-is-this-extension-useful)
- [API](api)


## Audience of this document

* Site administrators (people who want to make functionality available to users)
* Developers (who want to build on this extension)

## What does this extension do?

By default you cannot link a contribution to a case in CiviCRM. This extension will
allow you to link a contribution to a case. 

* On the manage case screen one can see all case contributions
* On the manage case screen one can create a new case contribution
* On the view contribution screen one can see the linked case and click on it to manage the case
* On the new/edit contribution screen one can link a contribution to an existing case

## When is this extension useful?

This extension is useful when you have for example a coaching contract and you log 
everything on a case. At the end you want to invoice the client of the case based 
on how many coaching sessions took place. 

## API

This extension provides an API for linking a contribution to a case. below is a description of the API calls.

### CaseContribution.create

This API call creates a link between a contribution and a case.


| Paramater       | Required | Description                |
|-----------------|----------|----------------------------|
| contribution_id | Yes      | The ID of the contribution |
| case_id         | Yes      | The ID of the case.        |

### CaseContribution.delete

This API call removes a link between a contribution and a case.

| Paramater       | Required | Description                |
|-----------------|----------|----------------------------|
| contribution_id | Yes      | The ID of the contribution |
| case_id         | Yes      | The ID of the case.        |

### CaseContribution.get

This API call gets all links between cases and contributions.

| Paramater       | Required | Description                |
|-----------------|----------|----------------------------|
| contribution_id | No       | The ID of the contribution |
| case_id         | No       | The ID of the case.        |


