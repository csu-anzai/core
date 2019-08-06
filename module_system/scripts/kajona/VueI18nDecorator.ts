import { createDecorator } from 'vue-class-component'
import Lang from 'core/module_system/scripts/kajona/Lang'
export const FetchLang = (modules : Array<string>) => {
    return createDecorator((component, key) => {
        component.created = async () => {
            let en = {}
            let de = {}
            await Promise.all(modules.map(async module => {
                en[module] = await Lang.fetchModule(module, 'en')
                de[module] = await Lang.fetchModule(module, 'de')
            }))
            window.i18n.setLocaleMessage('en', en)
            window.i18n.setLocaleMessage('de', de)
        }
    })
}
