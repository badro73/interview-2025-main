Feature: Financial Lifecycle Management
  In order to manage my money
  As a business partner
  I want to be able to create accounts, deposit funds, exchange currencies, and withdraw money.

  Background:
    Given I add "Content-Type" header equal to "application/json"
    And I add "Accept" header equal to "application/json"
    Given there is a business partner with data:
      | name       | status | legalForm        | address          | city  | zip   | country |
      | Badreddine | active | natural_person   | 123 Symfony Lane | Paris | 75001 | France  |

  Scenario: Full lifecycle: Account creation, Payin, Exchange, and Payout
    # Step 1: Create a CHF account
    Given there is an account with data:
      | balance | currency | businessPartner          |
      | 0.00    | CHF      | /api/business_partners/1 |

    # Step 2: Deposit 1000 CHF (Payin)
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/json"
    When I send a "POST" request to "/api/transactions/payin" with body:
    """
    {
        "amount": "1000.00",
        "name": "Initial Deposit",
        "date": "2024-05-20T10:00:00Z",
        "country": "CH",
        "iban": "CH123456789",
        "account": "/api/accounts/1"
    }
    """
    Then the response status code should be 201

    # Step 3: Exchange 1000 CHF for EUR
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/json"
    When I send a "POST" request to "/api/transactions/exchange" with body:
    """
    {
      "fromCurrency": "CHF",
      "toCurrency": "EUR",
      "amount": "1000.00",
      "businessPartnerId": 1
    }
    """
    Then the response status code should be 201

    # Step 4: Withdraw the resulting 1100 EUR (Payout)
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/json"
    When I send a "POST" request to "/api/transactions/payout" with body:
    """
    {
      "name": "ATM Withdrawal",
      "amount": "1100.00",
      "type": "PAYOUT",
      "account": "/api/accounts/2",
      "date": "2025-01-02 12:00:00",
      "executed": false,
      "country": "FR",
      "iban": "FR998877"
    }
    """
    Then the response status code should be 201

    # --- CHF ACCOUNT VERIFICATION ---
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    When I send a "GET" request to "/api/transactions?account.id=1"
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to 2
    # First transaction: Initial Deposit (Payin +1000)
    And the JSON node "hydra:member[0].type" should be equal to "payin"
    And the JSON node "hydra:member[0].amount" should be equal to "1000.00"
    # Second transaction: Currency Exchange Out (Payout/Exchange -1000)
    And the JSON node "hydra:member[1].type" should be equal to "payout"
    And the JSON node "hydra:member[1].amount" should be equal to "1000.00"

    # --- EUR ACCOUNT VERIFICATION ---
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    When I send a "GET" request to "/api/transactions?account.id=2"
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to 2
    # First transaction: Currency Exchange In (Payin/Exchange +1100)
    And the JSON node "hydra:member[0].type" should be equal to "payin"
    And the JSON node "hydra:member[0].amount" should be equal to "1100.00"
    # Second transaction: ATM Withdrawal (Payout -1100)
    And the JSON node "hydra:member[1].type" should be equal to "payout"
    And the JSON node "hydra:member[1].amount" should be equal to "1100.00"

    # --- FINAL BALANCE VERIFICATION ---
    When I send a "GET" request to "/api/accounts/1"
    Then the JSON node "balance" should be equal to "0.00"

    When I send a "GET" request to "/api/accounts/2"
    Then the JSON node "balance" should be equal to "0.00"
