const { src, dest }     = require('gulp');
const postcss           = require('gulp-postcss');
const postcssEasyImport = require('postcss-easy-import');
const postcssPresetEnv  = require('postcss-preset-env');
const rename            = require('gulp-rename');
const cssnano           = require('cssnano');
const discardComments   = require('postcss-discard-comments');
const beautify          = require('gulp-jsbeautifier');
const minify            = require('gulp-minify');
const zip               = require('gulp-zip');

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
    .pipe(beautify())
    .pipe(dest('assets/css'))
    .pipe(rename({suffix: '.min'}))
    .pipe(postcss([
        cssnano(),
        discardComments()
    ]))
    .pipe(dest('assets/css'))
}

function scripts() {
  return src('src/scripts/**/*.js')
    .pipe(beautify())
    .pipe(dest('assets/js'))
    .pipe(minify({
      ext:{
        min:'.min.js'
      }
    }))
    .pipe(dest('assets/js'))
}

function compress() {
  return src([
      "./**",
      "!src",
      "!src/**",
      "!_dist",
      "!_dist/**",
      "!node_module/",
      "!node_modules/**",
      "!gulpfile.js",
      "!*.dist",
      "!*.log",
      "!*.DS_Store",
      "!*.gitignore",
      "!*.git",
      "!*.json"
    ])
    .pipe(zip('welcomewp.zip'))
    .pipe(dest('./_dist'))
}

exports.styles = styles
exports.scripts = scripts
exports.compress = compress