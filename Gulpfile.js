var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var run = require('gulp-run');
var notify = require('gulp-notify');

gulp.task('test', function() {
   gulp.src('spec/**/*.php')
       .pipe(run('clear'))
       .pipe(phpspec('', { notify: true }))
       .on('error', notify.onError({
           title: 'Oh shit!',
           message: 'Your tests failed!'
       }))
       .pipe(notify({
           title: 'Success',
           message: 'All tests have returned green!'
       }));
});

gulp.task('watch', function() {
   gulp.watch(['spec/**/*.php', 'src/**/*.php'], ['test']);
});

gulp.task('default', ['test', 'watch']);
