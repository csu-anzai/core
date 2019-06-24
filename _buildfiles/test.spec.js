import { mount } from '@vue/test-utils'
// import Autocomplete from '../../module_system/scripts/components/Autocomplete/Autocomplete.vue'
import Test from '../module_search/scripts/components/Searchbar/Test.vue'
describe('Autocomplete', () => {
    // Now mount the component and you have the wrapper
    const wrapper = mount(Test)
    console.log('wrapper : ', wrapper.vm)

    // it('renders the correct markup', () => {
    //   expect(wrapper.html()).toContain('<span class="count">0</span>')
    // })

    // it's also easy to check for the existence of elements
    it('has a button', () => {
        expect(wrapper.contains('input')).to.be.true
    })
})
