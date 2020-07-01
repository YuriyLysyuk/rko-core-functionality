const gulp = require("gulp"),
  rename = require("gulp-rename"),
  sourcemaps = require("gulp-sourcemaps"),
  terser = require("gulp-terser");
gulp.task("rko-scripts", function () {
  return gulp
    .src([
      "rko-calc/assets/js/src/rko-calc.js",
    ])
    .pipe(sourcemaps.init()) // Начинаем запись карты
    .pipe(terser()) // Сжимаем JS файл
    .pipe(rename({ suffix: ".min" })) // Добавляем .min
    .pipe(sourcemaps.write("./")) // Заканчиваем запись карты
    .pipe(gulp.dest("rko-calc/assets/js")); // Выгружаем в папку
});

gulp.task("watch", function () {
  gulp.watch(
    ["rko-calc/assets/js/src/rko-calc.js"],
    gulp.parallel("rko-scripts")
  );
});
gulp.task("default", gulp.parallel("rko-scripts", "watch"));
