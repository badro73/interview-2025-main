Feature: Currency Exchange
  In order to convert funds between different currencies
  As a user
  I need to be able to perform currency exchange through the REST API

  Background:
    Given I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    And there is a business partner with data:
      | name  | status | legalForm                 | address | city   | zip  | country |
      | AMNIS | active | limited_liability_company | Street  | Zürich | 8048 | CH      |
    And there is an account with data:
      | balance | currency | businessPartner          |
      | 1000    | CHF      | /api/business_partners/1 |
      | 0       | EUR      | /api/business_partners/1 |

  Scenario: Exchange CHF to EUR
    When I send a POST request to "/api/transactions/exchange" with body:
    """
      {
        "fromCurrency": "CHF",
        "toCurrency": "EUR",
        "amount": "1000.00",
        "businessPartnerId": 1
      }
    """
    Then the response status code should be 201
    When I send a GET request to "/api/accounts/1"
    Then the JSON node "balance" should be equal to "0.00"
    When I send a GET request to "/api/accounts/2"
    Then the JSON node "balance" should be equal to "1100.00"
