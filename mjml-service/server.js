const express = require('express')
const mjml2html = require('mjml')

const app = express()

app.use(express.json())

app.post('/parse', (req, res) => {
    const { mjml } = req.body

    try {
        if (!mjml) {
            throw new Error('Nothing sent under \'mjml\' key in request!')
        }

        const { html } = mjml2html(mjml, {
            validation: 'strict',
            keepComments: true,
            beautify: true
        })

        return res.status(200).json({
            'status': 'success',
            'data': {
                html
            }
        })
    }
    catch (e) {
        return res.status(400).json({
            'status': 'fail',
            'message': e.message
        })
    }
})

const port = 3000
app.listen(port, () => {
    console.log(`listening on port ${port}!`)
})
