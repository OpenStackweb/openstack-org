module.exports = (err, stats) => {
	if(err) {
		console.err(err);
	}
    console.log('[webpack:build]', stats.toString({
        chunks: false,
        colors: true
    }));
};