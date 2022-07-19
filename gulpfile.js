const { src, dest }     = require('gulp');
const postcss           = require('gulp-postcss');
const postcssEasyImport = require('postcss-easy-import');
const postcssPresetEnv  = require('postcss-preset-env');
const rename            = require('gulp-rename');
const cssnano           = require('cssnano');
const discardComments   = require('postcss-discard-comments');

function styles(cb) {
  const postcssPlugins = [
      postcssEasyImport(),
      postcssPresetEnv({
          stage: 3,
          features: {
              'nesting-rules': true,
          },
          browsers: 'last 2 versions'
      })
  ];

return src('src/styles/**/*.css')
    .pipe(postcss(postcssPlugins))
    .pipe(dest('assets/css'))
    .pipe(rename({suffix: '.min'}))
    .pipe(postcss([
        cssnano(),
        discardComments()
    ]))
    .pipe(dest('assets/css'))
}

exports.styles = styles