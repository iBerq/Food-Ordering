# Food Ordering
CS353 Database Systems Term Project

**Project Members:**
  - Ahmet Ayberk Yılmaz
  - Süleyman Semih Demir

# Description
A food ordering application implemented for a school project.

Developed an web application project which implemented with HTML, CSS, and PHP that uses MySQL for database and has the functionalities below.

  - Signup and Login functionalities for 3 user types (Customer, Delivery guy, and owner).

**Customer makes an order from a restaurant.**
  - Search restaurants and meals in the system through an interface.
  - Order meal/s from a particular restaurant by specifying options such as
adding/removing certain ingredients, delivery time, and etc.
  - List all the orders made by the customer on a history page. Not delivered orders
should have an order cycle having different status values.
  - Check details of a particular order by listing meals purchased, restaurant served,
delivery time, delivery person, and etc.
  - Write comments for orders that are finalized/delivered and see the response of the
restaurant's owner if available.

**The Restaurant Owner manages his/her menu and orders.**
  - List all the orders made by customers considering different values for meal status.
  - Finalize an order and ask for a delivery guy assignment. A delivery guy is
randomly assigned to an order if his/her status is available.
  - List all the comments made by customers and write a response to each comment.
  - Add/Remove and/or modify meals inside the menu. Modification can include
price change, change of ingredients, and/or managing delivery options.

**The Delivery guy delivers the order.**
  - List all the orders handled by the delivery guy including assignment requests
highlighting the decision if available.
  - Specificity either regions or restaurants to work with to be assigned orders if
available.
  - Accept/Reject delivery assignments. If an assignment is accepted, status of the
delivery guy should be changed to “not_available” and all the other assignments
waiting for a decision should be automatically rejected.
  - Finalize an order.

