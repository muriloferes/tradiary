module.exports = {
    'env': {
		'browser': true,
		'es6': true
    },
    'parserOptions': {
		'ecmaVersion': 2018
    },
    'rules': {
		'block-spacing': 'error',
		'indent': [
			'error',
			'tab',
			{ 'SwitchCase': 1 }
		],
		'key-spacing': ['error', {
			'beforeColon': false,
			'afterColon': true
		}],
		'keyword-spacing': ['error', {
			'before': false,
			'after': false,
			'overrides': {
				'case': { 'before': false, 'after': true },
				'return': { 'before': false, 'after': true }
			}
		}],
		'no-multiple-empty-lines': ['error', {
			'max': 1
		}],
		//'no-unused-vars': 'error',
		'no-var': 'error',
		'object-curly-spacing': ['error', 'always'],
		'prefer-const': ['error', {
            'destructuring': 'any',
            'ignoreReadBeforeAssign': false
		}],
		'quotes': [
			'error',
			'single'
		],
		'semi': [
			'error',
			'always'
        ],
		'space-before-blocks': ['error', 'never']
	}
};
