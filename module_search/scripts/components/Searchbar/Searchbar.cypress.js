beforeEach(() => {
    cy.visit('http://localhost/agp-core-project')
})
describe('Searchbar Component', () => {
    it('Login and open Searchbar modal', () => {
        cy.get('input#name').type('dha')
        cy.get('input#passwort').type('admin0815')
        cy.get('button').click()
        cy.wait(5000)
        cy.get('input.search-query:first').click()
        cy.get('input#searchbarInput').type('admin')
        cy.wait(5000)
    })
})
