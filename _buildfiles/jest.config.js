module.exports = {
    rootDir: '../../',
    // // moduleDirectories: ['<rootDir>/core/_buildfiles/node_modules'],
    moduleFileExtensions: [
        'js',
        'ts',
        'vue'
    ],
    transform: {
        '^.+\\.tsx?$': '<rootDir>/core/_buildfiles/node_modules/ts-jest',
        // '^.+\\.ts$': '<rootDir>/core/_buildfiles/node_modules/babel-jest',
        '.*\\.(vue)$': '<rootDir>/core/_buildfiles/node_modules/vue-jest',
        '^.+\\.js$': '<rootDir>/core/_buildfiles/node_modules/babel-jest'
    },
    // // scriptPreprocessor: '<rootDir>/core/_buildfiles/node_modules/babel-jest',
    // // 'moduleFileExtensions': ['js', 'json', 'jsx' ],
    // // 'moduleNameMapper': {
    // //     '^.*[.](jpg|JPG|gif|GIF|png|PNG|less|LESS|css|CSS)$': 'EmptyModule'
    // // },
    // transformIgnorePatterns: [ '/node_modules/' ] ,
    verbose: true,
    // moduleDirectories: ['node_modules'],
    globals: {
        'NODE_ENV': 'test'
    }

    // unmockedModulePathPatterns: [
    //     '<rootDir>/core/_buildfiles/node_modules/vue',
    //     '<rootDir>/core/_buildfiles/node_modules/vue-property-decorator'
    //     // '<rootDir>/node_modules/react-addons-test-utils',
    //     // '<rootDir>/EmptyModule.js'
    // ]
}
