TODO
=============

Plans
----------
- OAuth Controller
- Authentication Controller for users and user groups
- Template Controller for allowing developers to use template files
- Expand upon the Data Controller
- Possibly creating a controller for providing a REST API for any web application and/or site
- Pagination Controller

Finished
----------


Rejected Ideas
-------------------
- Possibly creating a parent class for Controllers, and auto-loading with reflection
  - This was an awful idea, we'll just let people use the WebCore::instance()->add($controller) method.
  - Also, I'm referring solely to the auto-loading part of this idea, the parent class wouldn't be as bad.
- AutoLoad Controller classes in a folder specified by the Controller's Name.
  - This should fall on the controllers, or the parent class.