Feature: Financial Lifecycle Management
  In order to manage my money
  As a business partner
  I want to be able to create accounts, deposit funds, exchange currencies, and withdraw money.

  Background:
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/json"
    And there is a business partner with data:
      | name       | status | legalForm        | address          | city  | zip   | country |
      | Badreddine | active | natural_person   | 123 Symfony Lane | Paris | 75001 | FR      |
     # Step 1: Create a CHF account
    And there is an account with data:
      | balance | currency | businessPartner          |
      | 0.00    | CHF      | /api/business_partners/1 |

  Scenario: Full lifecycle: Account creation, Payin, Exchange, and Payout
    # --- STEP 2: Initial Deposit ---
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

    # --- STEP 5: Payout Execution ---
    Given I add "Content-Type" header equal to "application/merge-patch+json"
    When I send a PATCH request to "/api/transactions/4/payout/execute" with body:
    """
      {

      }
    """
    Then the response status code should be 200
    And the JSON node "executed" should be true

    # --- GLOBAL VERIFICATION (Transactions & Balances) ---
    Given I add "Content-Type" header equal to "application/ld+json"
    And I add "Accept" header equal to "application/ld+json"
    When I send a "GET" request to "/api/transactions"
    Then the response status code should be 200
    And the JSON node "hydra:totalItems" should be equal to 4
    # Check types and order
    And the JSON node "hydra:member[0].type" should be equal to "payin"
    And the JSON node "hydra:member[1].type" should be equal to "exchange"
    And the JSON node "hydra:member[2].type" should be equal to "exchange"
    And the JSON node "hydra:member[3].type" should be equal to "payout"

    # Final balances must be zero
    When I send a "GET" request to "/api/accounts/1"
    Then the JSON node "balance" should be equal to "0.00"
    When I send a "GET" request to "/api/accounts/2"
    Then the JSON node "balance" should be equal to "0.00"
