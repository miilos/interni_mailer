const express = require('express')
const OpenAI = require('openai')

const app = express()
const openai = new OpenAI({
    apiKey: process.env.OPENAI_API_KEY
})

const MODEL = 'gpt-4o-mini'

app.use(express.json())

app.post('/prompt', async (req, res) => {
    try {
        const { prompt } = req.body

        const chatResponse = await openai.responses.create({
            model: MODEL,
            instructions: 'Respond only with the code in plain text',
            input: prompt
        })

        res.status(200).json({
            'status': 'success',
            'data': {
                'response': chatResponse.output_text
            }
        })
    } catch (err) {
        res.status(500).json({
            'status': 'error',
            'message': err.message
        })
    }
})

const port = 4000
app.listen(port, () => {
    console.log(`listening on port ${port}!`)
})
