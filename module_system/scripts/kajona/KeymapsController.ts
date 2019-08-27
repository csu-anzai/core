/**
 * Class to register all the user keymaps
 */
class KeymapsController {
    public static pressedKey : KeyboardEvent
    public static init () : void {
        document.body.addEventListener('keydown', this.onKeyDown)
    }

    /** emits an event on key press
     *
     * @param key the pressed key
     * @param event the event to emit on key press
     */
    public static emitEvent (key : string, event: string) : void {
        console.log('emit ', event, ' key : ', key)
    }

    public static onKeyDown (e : KeyboardEvent) : void {
        this.pressedKey = e
        this.emitEvent('f', 'openSearchbar')
    }
}

export default KeymapsController
